<x-dashboard.layout title="Cover Letters">
    <x-slot name="header">
        <x-dashboard.page-header
            title="Cover Letters"
            description="Draft personalized cover letters linked to your CVs."
            class="mb-0"
        />
    </x-slot>

    @if (session('status') === 'cover-letter-deleted')
        <div class="dash-alert-success">Cover letter deleted.</div>
    @endif

    @php($pdfFlashError = session('cover_letter_pdf_error'))
    @if (is_array($pdfFlashError) && ! empty($pdfFlashError['message']))
        <div class="dash-alert-error" role="alert">{{ $pdfFlashError['message'] }}</div>
    @endif

    <div
        x-data="coverLetterCreateModal({
            optionsUrl: @js(route('cover-letters.cv-options')),
            createCvUrl: @js(route('dashboard.cvs')),
        })"
        @keydown.escape.window="open && close()"
        class="mb-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between"
    >
        <p class="text-sm text-muted">
            {{ $coverLetters->count() }} {{ Str::plural('cover letter', $coverLetters->count()) }}
        </p>

        <button
            type="button"
            @click="show()"
            data-create-cover-letter
            class="dash-btn-primary w-full justify-center px-3 py-2 sm:w-auto"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Cover Letter
        </button>

        {{-- Choose a CV modal --}}
        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:px-4 sm:py-6"
            role="dialog"
            aria-modal="true"
            aria-labelledby="cl-create-modal-title"
            data-cv-picker-modal
        >
            <div class="fixed inset-0 bg-slate-900/50" @click="close()" aria-hidden="true"></div>

            <div class="relative flex max-h-[92vh] w-full max-w-lg flex-col rounded-t-2xl border border-line bg-white shadow-xl sm:max-h-[85vh] sm:rounded-2xl">
                <div class="flex items-start justify-between gap-3 border-b border-line px-5 pb-4 pt-5 sm:px-6">
                    <div class="min-w-0">
                        <h2 id="cl-create-modal-title" class="text-lg font-semibold text-navy">Choose a CV</h2>
                        <p class="mt-1 text-sm text-muted">Your cover letter will be tailored using the CV you select.</p>
                    </div>
                    <button type="button" @click="close()" class="shrink-0 rounded-lg p-1.5 text-muted hover:bg-cream" aria-label="Close">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-6">
                    {{-- Loading --}}
                    <div x-show="state === 'loading'" class="space-y-3" data-cv-picker-loading aria-live="polite">
                        <span class="sr-only">Loading your CVs</span>
                        <template x-for="n in 3" :key="n">
                            <div class="h-16 animate-pulse rounded-xl bg-cream"></div>
                        </template>
                    </div>

                    {{-- Error --}}
                    <div x-show="state === 'error'" x-cloak class="rounded-xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-700" role="alert" data-cv-picker-error>
                        <p>We couldn't load your CVs. Please check your connection and try again.</p>
                        <button type="button" @click="load()" class="mt-3 rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-red-700 ring-1 ring-red-200 hover:bg-red-100">Try again</button>
                    </div>

                    {{-- Empty --}}
                    <div x-show="state === 'ready' && cvs.length === 0" x-cloak class="py-6 text-center" data-cv-picker-empty>
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                            @include('web.components.dashboard.icons.mail')
                        </span>
                        <h3 class="mt-3 text-base font-semibold text-navy">You don't have any CVs yet</h3>
                        <p class="mt-1 text-sm text-muted">Every cover letter is built from one of your CVs. Create a new CV or upload an existing one to get started.</p>
                        <a :href="createCvUrl" class="dash-btn-primary mt-5 justify-center">Create or upload a CV</a>
                    </div>

                    {{-- List --}}
                    <div x-show="state === 'ready' && cvs.length > 0" x-cloak>
                        <label for="cl-cv-search" class="sr-only">Search your CVs</label>
                        <input
                            id="cl-cv-search"
                            x-ref="search"
                            x-model="search"
                            type="search"
                            placeholder="Search by CV name, job title, name or location"
                            autocomplete="off"
                            data-cv-picker-search
                            class="w-full rounded-lg border border-line bg-white px-3 py-2 text-sm text-navy focus:border-brand-400 focus:outline-none"
                        >

                        <ul class="mt-3 space-y-2" role="listbox" aria-label="Your CVs">
                            <template x-for="cv in filtered" :key="cv.id">
                                <li>
                                    <button
                                        type="button"
                                        role="option"
                                        :aria-selected="selectedId === cv.id"
                                        @click="select(cv)"
                                        @dblclick="select(cv); proceed()"
                                        :data-cv-id="cv.id"
                                        :class="selectedId === cv.id ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500' : 'border-line hover:bg-cream/60'"
                                        class="flex w-full min-w-0 items-start gap-3 rounded-xl border px-4 py-3 text-left transition"
                                    >
                                        <span
                                            class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border"
                                            :class="selectedId === cv.id ? 'border-brand-600 bg-brand-600 text-white' : 'border-line bg-white'"
                                            aria-hidden="true"
                                        >
                                            <svg x-show="selectedId === cv.id" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-sm font-semibold text-navy" x-text="cv.title"></span>
                                            <span class="mt-0.5 block truncate text-xs text-muted" x-text="[cv.full_name, cv.professional_title].filter(Boolean).join(' - ') || 'No name added yet'"></span>
                                            <span class="mt-0.5 block truncate text-xs text-muted" x-text="[cv.template, cv.updated_human ? 'Updated ' + cv.updated_human : null].filter(Boolean).join(' | ')"></span>
                                        </span>
                                    </button>
                                </li>
                            </template>
                        </ul>

                        <p x-show="filtered.length === 0" class="mt-4 text-center text-sm text-muted" data-cv-picker-no-results>
                            No CVs match "<span x-text="search"></span>".
                        </p>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-line px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                    <button type="button" @click="close()" class="dash-btn-secondary justify-center">Cancel</button>
                    <button
                        type="button"
                        @click="proceed()"
                        :disabled="! selected || navigating"
                        data-cv-picker-continue
                        class="dash-btn-primary justify-center disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span x-text="navigating ? 'Opening...' : 'Continue'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($coverLetters->isEmpty())
        <div class="dash-empty">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                @include('web.components.dashboard.icons.mail')
            </span>
            <h2 class="mt-4 text-lg font-semibold text-navy">No cover letters yet</h2>

            @if ($cvs->isEmpty())
                <p class="mt-2 text-sm text-muted">Create a CV first — every cover letter is generated from one of your CVs.</p>
                <a href="{{ route('dashboard.cvs') }}" class="dash-btn-primary mt-6">Go to My CVs</a>
            @else
                <p class="mt-2 text-sm text-muted">Click Create Cover Letter and pick one of your CVs to generate a tailored cover letter in minutes.</p>
            @endif
        </div>
    @else
        <div class="dash-table-wrap">
            <div class="dash-table-scroll">
                <table class="dash-table text-sm">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Linked CV</th>
                            <th>Company</th>
                            <th>Job Title</th>
                            <th class="hidden md:table-cell">Template</th>
                            <th class="hidden sm:table-cell">Created</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coverLetters as $coverLetter)
                            <tr class="hover:bg-cream/40">
                                <td>
                                    <a href="{{ route('cover-letters.edit', $coverLetter) }}" class="font-semibold text-navy hover:text-brand-600">
                                        {{ $coverLetter->title }}
                                    </a>
                                </td>
                                <td class="max-w-[10rem] truncate text-muted">{{ $coverLetter->cv?->title ?? '—' }}</td>
                                <td class="max-w-[8rem] truncate text-muted">{{ $coverLetter->company_name ?: '—' }}</td>
                                <td class="max-w-[8rem] truncate text-muted">{{ $coverLetter->job_title ?: '—' }}</td>
                                <td class="hidden text-muted md:table-cell">{{ $coverLetter->template?->name ?? '—' }}</td>
                                <td class="hidden whitespace-nowrap text-muted sm:table-cell">{{ $coverLetter->created_at?->format('M j, Y') }}</td>
                                <td>
                                    <div class="flex flex-wrap items-center justify-end gap-1 sm:gap-2">
                                        <a
                                            href="{{ route('cover-letters.edit', $coverLetter) }}"
                                            class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand-700 hover:bg-brand-50"
                                        >Edit</a>

                                        <form method="POST" action="{{ route('cover-letters.duplicate', $coverLetter) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-navy hover:bg-cream">
                                                Duplicate
                                            </button>
                                        </form>

                                        <div
                                            x-data="coverLetterPdfDownload({
                                                url: @js(route('pdf-exports.cover-letter.download', $coverLetter)),
                                                filename: @js((Str::slug($coverLetter->title ?: 'cover-letter') ?: 'cover-letter').'.pdf'),
                                                initialError: @js(is_array($pdfFlashError) && ($pdfFlashError['slug'] ?? null) === $coverLetter->slug ? $pdfFlashError['message'] : null),
                                            })"
                                            class="relative"
                                            data-pdf-download
                                        >
                                            <button
                                                type="button"
                                                @click="download()"
                                                :disabled="status === 'loading'"
                                                :aria-busy="status === 'loading'"
                                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-navy hover:bg-cream disabled:cursor-wait disabled:opacity-60"
                                            >
                                                <svg x-show="status !== 'loading'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12M12 16.5V3" />
                                                </svg>
                                                <svg x-show="status === 'loading'" x-cloak class="h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                                </svg>
                                                <span x-text="status === 'loading' ? 'Preparing...' : 'Download PDF'">Download PDF</span>
                                            </button>
                                            <div
                                                x-show="status === 'error' && error"
                                                x-cloak
                                                role="alert"
                                                data-pdf-error
                                                class="absolute right-0 top-full z-10 mt-1 w-64 rounded-lg border border-red-200 bg-red-50 p-3 text-left text-xs text-red-700 shadow-lg"
                                            >
                                                <p x-text="error"></p>
                                                <div class="mt-2 flex gap-2">
                                                    <button type="button" @click="download()" data-pdf-retry class="rounded-md bg-white px-2.5 py-1 font-semibold text-red-700 ring-1 ring-red-200 hover:bg-red-100">Retry</button>
                                                    <button type="button" @click="status = 'idle'; error = null" class="rounded-md px-2 py-1 font-medium text-red-600 hover:bg-red-100">Dismiss</button>
                                                </div>
                                            </div>

                                            <x-cv.pdf-ready-sheet :id="'cover-letter-pdf-ready-'.$coverLetter->id" />
                                        </div>

                                        <form
                                            method="POST"
                                            action="{{ route('cover-letters.destroy', $coverLetter) }}"
                                            onsubmit="return confirm('Delete this cover letter? This cannot be undone.');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-dashboard.layout>
