<div class="flex h-full min-h-0 w-full flex-col">
    <div class="flex shrink-0 flex-wrap items-center justify-between gap-x-4 gap-y-1 border-b border-line bg-white px-4 py-2.5">
        <div class="flex items-center gap-2">
            <span class="inline-flex h-2 w-2 rounded-full bg-brand-600"></span>
            <span class="text-xs font-semibold uppercase tracking-wide text-navy">Live Preview</span>
        </div>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted">
            <span x-show="activeTemplateName()">
                Template: <span class="font-semibold text-navy" x-text="activeTemplateName()"></span>
            </span>
            <span>Updates as you type</span>
        </div>
    </div>

    <div class="relative min-h-0 flex-1 overflow-hidden bg-white">
        <iframe
            x-ref="previewFrame"
            class="absolute inset-0 h-full w-full border-0 bg-white"
            scrolling="yes"
            title="CV live preview"
        ></iframe>
    </div>
</div>
