@props([
    'name',
    'description' => null,
    'previewSrc',
    'fontLabel' => null,
    'fontSize' => null,
    'primary' => '#172d40',
    'secondary' => '#c1623d',
    'background' => '#ffffff',
    'text' => '#3a4a58',
    'templateKey',
    'action',
    'cta' => 'Use this template',
])

<article
    data-template-key="{{ $templateKey }}"
    class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-2xl"
>
    {{-- Preview surface --}}
    <div class="relative overflow-hidden border-b border-slate-100 bg-gradient-to-b from-slate-50 to-slate-200 p-5 sm:p-6">
        <div class="transition-transform duration-300 ease-out group-hover:scale-[1.02]">
            <x-template-preview
                :src="$previewSrc"
                class="mx-auto rounded-lg shadow-lg ring-1 ring-slate-900/10"
            />
        </div>
    </div>

    {{-- Info --}}
    <div class="flex flex-1 flex-col p-5">
        <h2 class="text-lg font-semibold text-navy" style="font-family:'Sora',sans-serif">{{ $name }}</h2>
        @if ($description)
            <p class="mt-1 line-clamp-2 text-sm leading-relaxed text-muted">{{ $description }}</p>
        @endif

        <div class="mt-4 flex items-end justify-between gap-4 border-t border-slate-100 pt-4">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Typography</p>
                <p class="mt-1 truncate text-sm font-medium text-navy">
                    {{ $fontLabel ?? 'Default' }}@if ($fontSize) <span class="text-muted">· {{ $fontSize }}</span>@endif
                </p>
            </div>
            <div class="shrink-0 text-right">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Colors</p>
                <div class="mt-1.5 flex items-center justify-end gap-1.5">
                    <span class="h-5 w-5 rounded-full ring-1 ring-slate-900/10" style="background: {{ $primary }}" title="Primary"></span>
                    <span class="h-5 w-5 rounded-full ring-1 ring-slate-900/10" style="background: {{ $secondary }}" title="Secondary"></span>
                    <span class="h-5 w-5 rounded-full ring-1 ring-slate-900/10" style="background: {{ $text }}" title="Text"></span>
                    <span class="h-5 w-5 rounded-full ring-1 ring-slate-900/10" style="background: {{ $background }}" title="Background"></span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ $action }}" class="mt-5">
            @csrf
            <input type="hidden" name="template" value="{{ $templateKey }}">
            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-brand-700 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
            >
                {{ $cta }}
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </button>
        </form>
    </div>
</article>
