import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import { prepareEditorHtml, sanitizeRichText } from './rich-text-sanitize';

const TOOLBAR_OPTIONS = [
    [{ header: [1, 2, 3, false] }],
    ['bold', 'italic', 'underline'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    [{ align: [] }],
    ['link'],
    ['clean'],
];

/** @type {WeakMap<HTMLElement, { quill: import('quill').default, onTextChange: () => void }>} */
const mountInstances = new WeakMap();

/**
 * Remove an existing Quill instance and duplicate toolbar/container markup.
 */
export function destroyRichTextEditor(mountEl) {
    if (! mountEl || ! (mountEl instanceof HTMLElement)) {
        return;
    }

    const tracked = mountInstances.get(mountEl);

    if (tracked?.quill) {
        try {
            if (tracked.onTextChange) {
                tracked.quill.off('text-change', tracked.onTextChange);
            }
        } catch {
            // Quill already torn down.
        }

        mountInstances.delete(mountEl);
    }

    try {
        const existing = Quill.find(mountEl);

        if (existing && existing !== tracked?.quill) {
            mountInstances.delete(mountEl);
        }
    } catch {
        // Mount node is not a Quill container yet.
    }

    const host = mountEl.parentElement;

    if (host) {
        host.querySelectorAll(':scope > .ql-toolbar').forEach((toolbar) => toolbar.remove());
    }

    mountEl.className = '';
    mountEl.removeAttribute('class');
    mountEl.innerHTML = '';
    delete mountEl.dataset.quillMounted;
}

/**
 * Create exactly one Quill instance for the mount element.
 */
export function createRichTextEditor(mountEl, options = {}) {
    destroyRichTextEditor(mountEl);

    const quill = new Quill(mountEl, {
        theme: 'snow',
        modules: {
            toolbar: options.toolbar ?? TOOLBAR_OPTIONS,
        },
        placeholder: options.placeholder ?? '',
    });

    mountEl.dataset.quillMounted = '1';

    return quill;
}

export function registerRichTextEditor() {
    window.Alpine.data('richTextField', (config = {}) => ({
        quill: null,
        syncing: false,
        minHeight: config.minHeight ?? '140px',
        _initialized: false,
        _mountEl: null,
        _onTextChange: null,

        init() {
            if (this._initialized) {
                return;
            }

            const getValue = typeof config.get === 'function' ? config.get : () => '';
            const setValue = typeof config.set === 'function' ? config.set : () => {};

            this._mountEl = this.$refs.editor;

            if (! this._mountEl) {
                return;
            }

            if (this._mountEl.dataset.quillMounted === '1' || Quill.find(this._mountEl)) {
                destroyRichTextEditor(this._mountEl);
            }

            this.quill = createRichTextEditor(this._mountEl, {
                placeholder: config.placeholder ?? '',
            });

            this._onTextChange = () => {
                if (this.syncing || ! this.quill) {
                    return;
                }

                const html = sanitizeRichText(this.quill.root.innerHTML);
                setValue(html);
                this.$el.dispatchEvent(new Event('input', { bubbles: true }));
            };

            this.quill.on('text-change', this._onTextChange);

            mountInstances.set(this._mountEl, {
                quill: this.quill,
                onTextChange: this._onTextChange,
            });

            this.applyEditorHeight();
            this.setEditorHtml(getValue());

            this.$watch(() => getValue(), (value) => {
                if (this.syncing || ! this.quill) {
                    return;
                }

                const next = prepareEditorHtml(value);
                const current = sanitizeRichText(this.quill.root.innerHTML);

                if (current !== next) {
                    this.setEditorHtml(next);
                }
            });

            this._initialized = true;
        },

        destroy() {
            this.teardownEditor();
        },

        teardownEditor() {
            if (this._mountEl) {
                destroyRichTextEditor(this._mountEl);
            }

            this.quill = null;
            this._onTextChange = null;
            this._mountEl = null;
            this._initialized = false;
            this.syncing = false;
        },

        applyEditorHeight() {
            const editor = this._mountEl?.querySelector('.ql-editor');

            if (editor) {
                editor.style.minHeight = this.minHeight;
            }
        },

        setEditorHtml(value) {
            if (! this.quill) {
                return;
            }

            this.syncing = true;
            const html = prepareEditorHtml(value);
            this.quill.root.innerHTML = html || '<p><br></p>';
            this.syncing = false;
        },
    }));
}
