<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobMatch\StoreJobMatchRequest;
use App\Jobs\AnalyzeJobMatchJob;
use App\Models\JobMatchScore;
use App\Services\CoverLetter\CoverLetterService;
use App\Services\Cv\CvService;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobMatchController extends Controller
{
    public function __construct(
        protected CvService $cvService,
        protected CoverLetterService $coverLetterService,
        protected SubscriptionService $subscriptionService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $jobMatchHistory = JobMatchScore::query()
            ->with(['cv:id,title', 'coverLetter:id,title'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $selectedJobMatchId = (int) $request->integer('report');
        $latestJobMatch = $selectedJobMatchId > 0
            ? $jobMatchHistory->firstWhere('id', $selectedJobMatchId)
            : $jobMatchHistory->first();

        return view('web.dashboard.job-match.index', [
            'cvs' => $this->cvService->listForUser($user),
            'coverLetters' => $this->coverLetterService->listForUser($user),
            'canViewFullResult' => ! $this->subscriptionService->resolveActivePlan($user)->isFree(),
            'latestJobMatch' => $latestJobMatch,
            'jobMatchHistory' => $jobMatchHistory,
        ]);
    }

    public function store(StoreJobMatchRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $jobMatchScore = JobMatchScore::query()->create([
            'user_id' => $request->user()->id,
            'cv_id' => (int) $validated['cv_id'],
            'cover_letter_id' => $validated['cover_letter_id'] ? (int) $validated['cover_letter_id'] : null,
            'job_description' => (string) $validated['job_description'],
            'status' => 'pending',
        ]);

        AnalyzeJobMatchJob::dispatch(
            job_match_score_id: (int) $jobMatchScore->id,
            user_id: (int) $request->user()->id,
            cv_id: (int) $validated['cv_id'],
            cover_letter_id: $validated['cover_letter_id'] ? (int) $validated['cover_letter_id'] : null,
            job_description: (string) $validated['job_description'],
        );

        return redirect()
            ->route('job-match')
            ->with('status', 'job-match-queued');
    }
}
