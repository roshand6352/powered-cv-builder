<x-dashboard.layout title="CVs">
    <x-slot name="header">
        <x-dashboard.page-header
            title="My CVs"
            description="Create, edit, and manage your resumes."
            class="mb-0"
        />
    </x-slot>

    @if (session('status') === 'cv-created')
        <div class="dash-alert-success">
            Your new CV has been created. Choose a template to continue.
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
        <p class="text-sm text-muted">{{ $cvs->count() }} {{ Str::plural('CV', $cvs->count()) }}</p>

        <form method="POST" action="{{ route('cvs.store') }}" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="dash-btn-primary w-full justify-center sm:w-auto">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create new CV
            </button>
        </form>
    </div>

    @if ($cvs->isEmpty())
        <div class="dash-empty">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                @include('web.components.dashboard.icons.document')
            </span>
            <h2 class="mt-4 text-lg font-semibold text-navy">No CVs yet</h2>
            <p class="mt-2 text-sm text-muted">Get started by creating your first AI-powered resume.</p>
            <form method="POST" action="{{ route('cvs.store') }}" class="mt-6">
                @csrf
                <button type="submit" class="dash-btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create your first CV
                </button>
            </form>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($cvs as $cv)
                @php
                    $statusLabel = ($cv->status === 2) ? 'published' : 'draft';
                @endphp
                <article class="dash-card min-w-0 p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="truncate text-lg font-semibold text-navy">{{ $cv->title }}</h2>
                            <p class="mt-1 text-sm text-muted">Updated {{ $cv->updated_at?->diffForHumans() }}</p>
                        </div>
                        <span @class([
                            'shrink-0 dash-badge',
                            'dash-badge-brand' => $statusLabel === 'published',
                            'bg-brand-100 text-brand-800' => $statusLabel !== 'published',
                        ])>
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @if ($cv->template_id === null)
                            <a href="{{ route('cvs.templates', $cv) }}" class="dash-btn-primary px-3 py-2">
                                Choose template
                            </a>
                        @else
                            <a href="{{ route('cvs.builder', $cv) }}" class="dash-btn-primary px-3 py-2">
                                Open builder
                            </a>
                            <a href="{{ route('cvs.templates', $cv) }}" class="dash-btn-secondary px-3 py-2">
                                Change template
                            </a>
                        @endif
                        <a href="{{ route('cvs.cover-letters.create', $cv) }}" class="dash-btn-secondary px-3 py-2">
                            Cover letter
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-dashboard.layout>
