/**
 * Open the shared AI upgrade modal used by CV Improve and Cover Letter Generate.
 *
 * @param {{ message?: string, url?: string }} detail
 */
export function openAiUpgradeModal(detail = {}) {
    window.dispatchEvent(new CustomEvent('open-ai-upgrade-modal', {
        detail: {
            message: detail.message
                ?? 'AI writing assistance is available on Pro plans. Upgrade to unlock this feature.',
            url: detail.url ?? '/dashboard/subscription',
        },
    }));
}
