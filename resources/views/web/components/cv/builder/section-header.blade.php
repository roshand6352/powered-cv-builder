@props(['section', 'title'])

<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-slate-900">{{ $title }}</h2>
    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
        <input
            type="checkbox"
            class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"
            :checked="isSectionEnabled('{{ $section }}')"
            @change="toggleSection('{{ $section }}')"
        >
        Show in preview
    </label>
</div>
