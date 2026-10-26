@props(['section', 'title'])

<div class="flex flex-wrap items-center justify-between gap-2">
    <h2 class="min-w-0 text-lg font-semibold text-slate-900">{{ $title }}</h2>
    <label class="inline-flex shrink-0 items-center gap-2 text-sm text-slate-600">
        <input
            type="checkbox"
            class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"
            :checked="isSectionEnabled('{{ $section }}')"
            @change="toggleSection('{{ $section }}')"
        >
        Show in preview
    </label>
</div>
