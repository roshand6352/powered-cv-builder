import './bootstrap';

import Alpine from 'alpinejs';
import { registerBuilderStore } from './cv/builder-store';
import { registerCvBuilder } from './cv/builder';
import { registerCvTemplatePicker } from './cv/template-picker';
import { registerCvLivePreview } from './cv/live-preview';
import { registerCoverLetterBuilder } from './cover-letter/builder';
import { registerRichTextEditor } from './shared/rich-text-editor';

window.Alpine = Alpine;

registerRichTextEditor();
registerBuilderStore();
registerCvBuilder();
registerCvTemplatePicker();
registerCvLivePreview();
registerCoverLetterBuilder();

Alpine.start();
