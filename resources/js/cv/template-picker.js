import { TemplateThumbnailRenderer } from './template-thumbnail-renderer';

export function registerCvTemplatePicker() {
    window.Alpine.data('cvTemplatePicker', (config) => ({
        templateCatalog: config.templateCatalog ?? [],
        sections: config.sections ?? {},
        templateThumbnails: {},
        thumbnailRenderer: new TemplateThumbnailRenderer(),
        thumbnailQueue: [],
        thumbnailBuildScheduled: false,
        thumbnailObserver: null,

        init() {
            this.$nextTick(() => {
                this.initGalleryThumbnailObserver();
            });
        },

        destroy() {
            this.thumbnailObserver?.disconnect();
        },

        queueTemplateThumbnail(templateKey) {
            if (! templateKey || this.templateThumbnails[templateKey]) {
                return;
            }

            const template = this.templateCatalog.find((item) => item.key === templateKey);

            if (template?.thumbnail) {
                return;
            }

            if (! this.thumbnailQueue.includes(templateKey)) {
                this.thumbnailQueue.push(templateKey);
            }

            this.scheduleThumbnailQueue();
        },

        scheduleThumbnailQueue() {
            if (this.thumbnailBuildScheduled) {
                return;
            }

            this.thumbnailBuildScheduled = true;

            const processNext = () => {
                const templateKey = this.thumbnailQueue.shift();

                if (! templateKey) {
                    this.thumbnailBuildScheduled = false;

                    return;
                }

                if (! this.templateThumbnails[templateKey]) {
                    this.templateThumbnails = {
                        ...this.templateThumbnails,
                        [templateKey]: this.thumbnailRenderer.build(
                            templateKey,
                            this.templateCatalog,
                            this.sections,
                        ),
                    };
                }

                if (this.thumbnailQueue.length) {
                    if (typeof requestIdleCallback === 'function') {
                        requestIdleCallback(processNext, { timeout: 600 });
                    } else {
                        setTimeout(processNext, 48);
                    }
                } else {
                    this.thumbnailBuildScheduled = false;
                }
            };

            if (typeof requestIdleCallback === 'function') {
                requestIdleCallback(processNext, { timeout: 800 });
            } else {
                setTimeout(processNext, 64);
            }
        },

        initGalleryThumbnailObserver() {
            if (typeof IntersectionObserver !== 'function') {
                this.templateCatalog.forEach((template) => {
                    this.queueTemplateThumbnail(template.key);
                });

                return;
            }

            const grid = this.$el.querySelector('.template-gallery-grid');

            if (! grid) {
                return;
            }

            this.thumbnailObserver?.disconnect();
            this.thumbnailObserver = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (! entry.isIntersecting) {
                            return;
                        }

                        const templateKey = entry.target.dataset.templateKey;

                        if (templateKey) {
                            this.queueTemplateThumbnail(templateKey);
                        }

                        this.thumbnailObserver?.unobserve(entry.target);
                    });
                },
                { rootMargin: '120px 0px' },
            );

            grid.querySelectorAll('[data-template-key]').forEach((element) => {
                this.thumbnailObserver.observe(element);
            });
        },
    }));
}
