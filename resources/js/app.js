import './bootstrap';

import Alpine from 'alpinejs';
import { registerBuilderStore } from './cv/builder-store';
import { registerCvBuilder } from './cv/builder';
import { registerCvTemplatePicker } from './cv/template-picker';
import { registerCvLivePreview } from './cv/live-preview';
import { registerCoverLetterBuilder } from './cover-letter/builder';
import { registerRichTextEditor } from './shared/rich-text-editor';
import { registerCheckoutCoupon } from './subscription/checkout-coupon';

window.Alpine = Alpine;

registerRichTextEditor();
registerBuilderStore();
registerCvBuilder();
registerCvTemplatePicker();
registerCvLivePreview();
registerCoverLetterBuilder();
registerCheckoutCoupon();

Alpine.start();
