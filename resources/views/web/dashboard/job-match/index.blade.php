<x-dashboard.layout title="Job Compatibility Checker">
    <x-slot name="header">
        <x-dashboard.page-header
            title="Job Compatibility Checker"
            description="Compare your CV against a job description using AI."
            class="mb-0"
        />
    </x-slot>

    @if (session('status') === 'job-match-queued')
        <div class="dash-alert-success">
            Job match analysis has been queued successfully.
        </div>
    @endif

    <div class="dash-card p-6 sm:p-8">
        <div class="max-w-3xl">
            <form
                method="POST"
                action="{{ route('job-match.store') }}"
                class="space-y-6"
                x-data="{
                    jobDescription: '',
                    cvId: '',
                    coverLetterId: '',
                    isLoading: false,
                    errors: {},
                    loadingMessages: [
                        'Analyzing your CV...',
                        'Extracting Job Requirements...',
                        'Comparing Skills...',
                        'Calculating Compatibility...',
                        'Preparing AI Suggestions...',
                    ],
                    loadingIndex: 0,
                    loadingInterval: null,
                    validate() {
                        this.errors = {};

                        if (!this.jobDescription.trim()) {
                            this.errors.jobDescription = 'Please paste the job description.';
                        }

                        if (!this.cvId) {
                            this.errors.cvId = 'Please select a CV.';
                        }

                        return Object.keys(this.errors).length === 0;
                    },
                    startLoading() {
                        this.isLoading = true;
                        this.loadingIndex = 0;
                        clearInterval(this.loadingInterval);
                        this.loadingInterval = setInterval(() => {
                            this.loadingIndex = (this.loadingIndex + 1) % this.loadingMessages.length;
                        }, 1200);
                    },
                    stopLoading() {
                        this.isLoading = false;
                        clearInterval(this.loadingInterval);
                        this.loadingInterval = null;
                        this.loadingIndex = 0;
                    },
                    submitForm() {
                        if (this.isLoading) {
                            return;
                        }

                        if (!this.validate()) {
                            return;
                        }

                        this.startLoading();
                        this.$nextTick(() => this.$el.submit());
                    },
                }"
                @submit.prevent="submitForm()"
            >
                @csrf
                <div>
                    <label for="job-description" class="mb-2 block text-sm font-medium text-navy">
                        Paste Job Description
                    </label>
                    <textarea
                        id="job-description"
                        name="job_description"
                        rows="10"
                        required
                        x-model="jobDescription"
                        @input="delete errors.jobDescription"
                        class="block w-full rounded-lg border border-line px-4 py-3 text-sm text-body shadow-sm transition placeholder:text-muted focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200"
                        placeholder="Paste the full job description here..."
                    ></textarea>
                    <p
                        x-show="errors.jobDescription"
                        x-text="errors.jobDescription"
                        class="mt-2 text-sm font-medium text-red-600"
                    ></p>
                    @error('job_description')
                        <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cv-id" class="mb-2 block text-sm font-medium text-navy">
                        CV Selection
                    </label>
                    <select
                        id="cv-id"
                        name="cv_id"
                        required
                        x-model="cvId"
                        @change="delete errors.cvId"
                        class="block w-full rounded-lg border border-line px-4 py-3 text-sm text-body shadow-sm transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200"
                    >
                        <option value="" selected disabled>Select a CV</option>
                        @foreach ($cvs as $cv)
                            <option value="{{ $cv->id }}">{{ $cv->title }}</option>
                        @endforeach
                    </select>
                    <p
                        x-show="errors.cvId"
                        x-text="errors.cvId"
                        class="mt-2 text-sm font-medium text-red-600"
                    ></p>
                    @error('cv_id')
                        <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cover-letter-id" class="mb-2 block text-sm font-medium text-navy">
                        Cover Letter
                    </label>
                    <select
                        id="cover-letter-id"
                        name="cover_letter_id"
                        x-model="coverLetterId"
                        class="block w-full rounded-lg border border-line px-4 py-3 text-sm text-body shadow-sm transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200"
                    >
                        <option value="">No Cover Letter</option>
                        @foreach ($coverLetters as $coverLetter)
                            <option value="{{ $coverLetter->id }}">{{ $coverLetter->title }}</option>
                        @endforeach
                    </select>
                    @error('cover_letter_id')
                        <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button
                        type="submit"
                        class="dash-btn-primary max-w-full disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="isLoading"
                    >
                        <span x-show="!isLoading">Analyze Job Match</span>
                        <span x-show="isLoading" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span>Analyzing…</span>
                        </span>
                    </button>
                    <p
                        x-show="isLoading"
                        x-text="loadingMessages[loadingIndex]"
                        class="mt-3 break-words text-sm text-muted"
                    ></p>
                </div>
            </form>
        </div>
    </div>

    @if ($latestJobMatch)
        <div class="dash-card mt-6 p-6 sm:p-8">
            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <h2 class="dash-section-title">Latest Result</h2>
                    <p class="dash-section-desc break-words">
                        CV: {{ $latestJobMatch->cv?->title ?? '—' }}
                        @if ($latestJobMatch->coverLetter)
                            · Cover Letter: {{ $latestJobMatch->coverLetter->title }}
                        @endif
                    </p>
                </div>

                @php
                    $statusClasses = match ($latestJobMatch->status) {
                        'completed' => 'dash-badge-brand',
                        'failed' => 'dash-badge bg-red-50 text-red-700',
                        'processing' => 'dash-badge bg-brand-100 text-brand-800',
                        default => 'dash-badge bg-cream text-muted',
                    };
                @endphp

                <span class="{{ $statusClasses }}">
                    {{ ucfirst($latestJobMatch->status) }}
                </span>
            </div>

            @if ($latestJobMatch->status === 'completed' && is_array($latestJobMatch->result_json))
                @php
                    $result = $latestJobMatch->result_json;
                    $score = max(0, min((int) ($result['score'] ?? 0), 100));
                    $matchedKeywords = collect($result['matched_keywords'] ?? [])->filter()->values();
                    $missingKeywords = collect($result['missing_keywords'] ?? [])->filter()->values();
                    $strengths = collect($result['strengths'] ?? [])->filter()->values();
                    $improvements = collect($result['improvements'] ?? [])->filter()->values();
                    $summary = (string) ($result['summary'] ?? '');
                @endphp

                <div class="grid gap-6 lg:grid-cols-[220px,1fr]">
                    <div class="flex flex-col items-center justify-start rounded-2xl bg-cream px-6 py-8 text-center">
                        <div
                            class="flex h-36 w-36 items-center justify-center rounded-full"
                            style="background: conic-gradient(#c1623d {{ $score }}%, #e5e7eb 0);"
                        >
                            <div class="flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white">
                                <span class="text-3xl font-bold text-navy">{{ $score }}%</span>
                                <span class="mt-1 text-xs font-medium uppercase tracking-wide text-muted">Match Score</span>
                            </div>
                        </div>
                        <p class="mt-4 text-sm text-muted">Overall compatibility based on your selected CV and job description.</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-line bg-white p-5">
                            <h3 class="text-base font-semibold text-navy">Matched Keywords</h3>
                            @if ($matchedKeywords->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($matchedKeywords as $keyword)
                                        <span class="dash-badge bg-emerald-50 text-emerald-700">{{ $keyword }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-3 text-sm text-muted">No matched keywords returned yet.</p>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-line bg-white p-5">
                            <h3 class="text-base font-semibold text-navy">Missing Keywords</h3>
                            @if ($missingKeywords->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($missingKeywords as $keyword)
                                        <span class="dash-badge bg-red-50 text-red-700">{{ $keyword }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-3 text-sm text-muted">No missing keywords returned yet.</p>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-line bg-white p-5">
                            <h3 class="text-base font-semibold text-navy">Strengths</h3>
                            @if ($strengths->isNotEmpty())
                                <ul class="mt-3 space-y-2 text-sm text-body">
                                    @foreach ($strengths as $strength)
                                        <li class="flex gap-2">
                                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
                                            <span>{{ $strength }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-3 text-sm text-muted">No strengths returned yet.</p>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-line bg-white p-5">
                            <h3 class="text-base font-semibold text-navy">Improvements</h3>
                            <div @class([
                                'mt-3',
                                'blur-sm select-none pointer-events-none' => ! $canViewFullResult,
                            ])>
                                @if ($improvements->isNotEmpty())
                                    <ul class="space-y-2 text-sm text-body">
                                        @foreach ($improvements as $improvement)
                                            <li class="flex gap-2">
                                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-brand-600"></span>
                                                <span>{{ $improvement }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-muted">No improvements returned yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if (! $canViewFullResult)
                    <div class="mt-6 rounded-2xl border border-brand-200 bg-brand-50 p-5 text-sm font-medium text-brand-800">
                        Upgrade to Pro to see exactly how to improve your CV for this role.
                    </div>
                @endif

                <div class="mt-6 rounded-2xl border border-line bg-white p-5">
                    <h3 class="text-base font-semibold text-navy">AI Summary</h3>
                    <div @class([
                        'mt-3',
                        'blur-sm select-none pointer-events-none' => ! $canViewFullResult,
                    ])>
                        <p class="text-sm leading-6 text-body">
                            {{ $summary !== '' ? $summary : 'No summary returned yet.' }}
                        </p>
                    </div>
                </div>
            @elseif ($latestJobMatch->status === 'failed')
                <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
                    The latest analysis failed. Please try again.
                </div>
            @else
                <div class="rounded-2xl border border-brand-200 bg-brand-50 p-5 text-sm text-brand-800">
                    Your latest job match analysis is currently {{ $latestJobMatch->status }}.
                </div>
            @endif
        </div>
    @endif

    <div class="dash-card mt-6 p-6 sm:p-8">
        <div class="mb-6">
            <h2 class="dash-section-title">Job Match History</h2>
            <p class="dash-section-desc">All previous analyses for your account, ordered from newest to oldest.</p>
        </div>

        @if ($jobMatchHistory->isEmpty())
            <div class="dash-empty">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                    @include('web.components.dashboard.icons.sparkles')
                </span>
                <h3 class="mt-4 text-base font-semibold text-navy">No analyses yet</h3>
                <p class="mt-2 text-sm text-muted">Run your first job match analysis to see history here.</p>
            </div>
        @else
            <div class="dash-table-wrap">
                <div class="dash-table-scroll">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>CV</th>
                                <th>Score</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jobMatchHistory as $jobMatch)
                                @php
                                    $statusClasses = match ($jobMatch->status) {
                                        'completed' => 'dash-badge-brand',
                                        'failed' => 'dash-badge bg-red-50 text-red-700',
                                        'processing' => 'dash-badge bg-brand-100 text-brand-800',
                                        default => 'dash-badge bg-cream text-muted',
                                    };
                                @endphp
                                <tr>
                                    <td class="whitespace-nowrap text-muted">
                                        {{ $jobMatch->created_at?->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="font-medium text-navy">
                                        {{ $jobMatch->cv?->title ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap text-navy">
                                        {{ $jobMatch->score !== null ? $jobMatch->score.'%' : '—' }}
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <span class="{{ $statusClasses }}">{{ ucfirst($jobMatch->status) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap text-right">
                                        <a
                                            href="{{ route('job-match', ['report' => $jobMatch->id]) }}"
                                            class="font-semibold text-brand-600 hover:text-brand-700"
                                        >
                                            Open report
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-dashboard.layout>
