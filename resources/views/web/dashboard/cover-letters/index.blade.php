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

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
        <p class="text-sm text-muted">
            {{ $coverLetters->count() }} {{ Str::plural('cover letter', $coverLetters->count()) }}
        </p>

        @if ($cvs->isNotEmpty())
            <div
                x-data="{ target: '' }"
                class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center"
            >
                <select
                    x-model="target"
                    class="w-full min-w-0 rounded-lg border border-line bg-white px-3 py-2 text-sm text-navy focus:border-brand-400 focus:outline-none sm:min-w-[12rem] sm:max-w-xs"
                >
                    <option value="">Select a CV…</option>
                    @foreach ($cvs as $cv)
                        <option value="{{ route('cvs.cover-letters.create', $cv) }}">{{ $cv->title }}</option>
                    @endforeach
                </select>
                <a
                    :href="target || '#'"
                    :class="target ? '' : 'pointer-events-none opacity-50'"
                    class="dash-btn-primary w-full justify-center px-3 py-2 sm:w-auto"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Cover Letter
                </a>
            </div>
        @endif
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
                <p class="mt-2 text-sm text-muted">Pick a CV above to generate a tailored cover letter in minutes.</p>
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

                                        <form method="POST" action="{{ route('pdf-exports.cover-letter.download', $coverLetter) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-navy hover:bg-cream">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12M12 16.5V3" />
                                                </svg>
                                                Download PDF
                                            </button>
                                        </form>

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
