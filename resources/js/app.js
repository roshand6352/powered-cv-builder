import './bootstrap';

import Alpine from 'alpinejs';
import { registerBuilderStore } from './cv/builder-store';
import { registerCvBuilder } from './cv/builder';
import { registerCvTemplatePicker } from './cv/template-picker';
import { registerCvLivePreview } from './cv/live-preview';
import { registerLinkedinPdfImportModal } from './cv/linkedin-pdf-import-modal';
import { registerCoverLetterBuilder } from './cover-letter/builder';
import { registerRichTextEditor } from './shared/rich-text-editor';
import { registerCheckoutCoupon } from './subscription/checkout-coupon';

window.Alpine = Alpine;

registerRichTextEditor();
registerBuilderStore();
registerCvBuilder();
registerCvTemplatePicker();
registerCvLivePreview();
registerLinkedinPdfImportModal();
registerCoverLetterBuilder();
registerCheckoutCoupon();

Alpine.start();
