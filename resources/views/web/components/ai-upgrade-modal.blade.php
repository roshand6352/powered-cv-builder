@props([
    'defaultUrl' => null,
])

@php
    $subscriptionUrl = $defaultUrl ?? route('dashboard.subscription');
@endphp

{{-- Shared AI upgrade prompt (CV Improve + Cover Letter Generate) --}}
<div
    x-data="{
        open: false,
        message: 'AI writing assistance is available on Pro plans. Upgrade to unlock this feature.',
        subscriptionUrl: @js($subscriptionUrl),
        show(detail = {}) {
            if (detail.message) {
                this.message = detail.message;
            }
            if (detail.url) {
                this.subscriptionUrl = detail.url;
            }
            this.open = true;
        },
        close() {
            this.open = false;
        },
        upgrade() {
            window.location.href = this.subscriptionUrl;
        },
    }"
    x-on:open-ai-upgrade-modal.window="show($event.detail || {})"
    x-cloak
>
    <div
        x-show="open"
        class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
        role="dialog"
        aria-modal="true"
        aria-labelledby="ai-upgrade-title"
    >
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/50"
            @click="close()"
        ></div>

        <div class="flex min-h-full items-center justify-center">
            <div
                x-show="open"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 sm:scale-95"
                @keydown.escape.window="open && close()"
                class="relative w-full max-w-md rounded-2xl border border-line bg-white p-6 shadow-xl"
            >
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <h2 id="ai-upgrade-title" class="text-lg font-semibold text-navy">Upgrade to unlock AI</h2>
                        <p class="mt-2 text-sm text-muted" x-text="message"></p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        @click="close()"
                        class="dash-btn-secondary w-full justify-center px-4 py-2 sm:w-auto"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="upgrade()"
                        class="inline-flex w-full items-center justify-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-700 sm:w-auto"
                    >
                        Upgrade Now
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
