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

    @if (session('status') === 'cv-deleted')
        <div class="dash-alert-success">
            CV deleted successfully.
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
        <div
            x-data="{
                confirmOpen: false,
                deleteUrl: '',
                deleting: false,
                errorMessage: '',
                openDelete(url) {
                    this.deleteUrl = url;
                    this.errorMessage = '';
                    this.confirmOpen = true;
                },
                closeDelete() {
                    if (this.deleting) return;
                    this.confirmOpen = false;
                    this.deleteUrl = '';
                    this.errorMessage = '';
                },
                async confirmDelete() {
                    if (this.deleting || !this.deleteUrl) return;

                    this.deleting = true;
                    this.errorMessage = '';

                    try {
                        const response = await fetch(this.deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Request failed');
                        }

                        // Reload so the flashed success message and updated list render.
                        window.location.reload();
                    } catch (error) {
                        this.deleting = false;
                        this.errorMessage = 'Failed to delete the CV. Please try again.';
                    }
                },
            }"
        >
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($cvs as $cv)
                    @php
                        $status = \App\Enums\CvStatus::resolve($cv->status);
                        $isSaved = $status === \App\Enums\CvStatus::Published;
                    @endphp
                    <article class="dash-card min-w-0 p-5 sm:p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="truncate text-lg font-semibold text-navy">{{ $cv->title }}</h2>
                                <p class="mt-1 text-sm text-muted">Updated {{ $cv->updated_at?->diffForHumans() }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <span @class([
                                    'dash-badge',
                                    'dash-badge-brand' => $isSaved,
                                    'bg-brand-100 text-brand-800' => ! $isSaved,
                                ])>
                                    {{ $status->label() }}
                                </span>
                                <button
                                    type="button"
                                    @click="openDelete(@js(route('cvs.destroy', $cv)))"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 transition hover:bg-red-50"
                                    title="Delete CV"
                                    aria-label="Delete CV"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
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

            {{-- Delete confirmation modal --}}
            <div
                x-show="confirmOpen"
                x-cloak
                class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
                role="dialog"
                aria-modal="true"
                aria-labelledby="delete-cv-title"
            >
                <div
                    x-show="confirmOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/50"
                    @click="closeDelete()"
                ></div>


                <div class="flex min-h-full items-center justify-center">
                    <div
                        x-show="confirmOpen"
                        x-transition:enter="ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-2 sm:scale-95"
                        @keydown.escape.window="closeDelete()"
                        class="relative w-full max-w-md rounded-2xl border border-line bg-white p-6 shadow-xl"
                    >
                        <h2 id="delete-cv-title" class="text-lg font-semibold text-navy">Delete CV</h2>
                        <p class="mt-2 text-sm text-muted">
                            Are you sure you want to delete this CV? This action cannot be undone.
                        </p>

                        <p
                            x-show="errorMessage"
                            x-cloak
                            x-text="errorMessage"
                            class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                            role="alert"
                        ></p>

                        <div class="mt-6 flex justify-end gap-3">
                            <button
                                type="button"
                                @click="closeDelete()"
                                :disabled="deleting"
                                class="dash-btn-secondary px-4 py-2 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                @click="confirmDelete()"
                                :disabled="deleting"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                <svg
                                    x-show="deleting"
                                    x-cloak
                                    class="h-4 w-4 animate-spin"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="deleting ? 'Deleting...' : 'Delete'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-dashboard.layout>
