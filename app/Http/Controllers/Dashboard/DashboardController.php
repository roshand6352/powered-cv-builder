<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Ai\AiUsageService;
use App\Services\CoverLetter\CoverLetterService;
use App\Services\Cv\CvService;
use App\Services\Dashboard\DashboardService;
use App\Services\Pdf\PdfExportService;
use App\Services\Subscription\PaymentService;
use App\Services\Subscription\PlanService;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
        protected CvService $cvService,
        protected CoverLetterService $coverLetterService,
        protected PdfExportService $pdfExportService,
        protected SubscriptionService $subscriptionService,
        protected PlanService $planService,
        protected PaymentService $paymentService,
        protected AiUsageService $aiUsageService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user()->loadCount(['cvs', 'coverLetters']);

        return view('web.dashboard.index', [
            'stats' => $this->dashboardService->getOverviewStats($user),
            'recentActivity' => $this->dashboardService->getRecentActivity($user),
        ]);
    }

    public function cvs(Request $request): View
    {
        return view('web.dashboard.cvs.index', [
            'cvs' => $this->cvService->listForUser($request->user()),
        ]);
    }

    public function coverLetters(Request $request): View
    {
        $cvs = $this->cvService->listForUser($request->user());

        return view('web.dashboard.cover-letters.index', [
            'coverLetters' => $this->coverLetterService->listForUser($request->user()),
            'cvs' => $cvs,
        ]);
    }

    public function subscription(Request $request): View
    {
        $user = $request->user();
        $currentPlan = $this->subscriptionService->resolveActivePlan($user);
        $plans = $this->planService->withCurrentPlan(
            $this->subscriptionService->availablePlans(),
            $currentPlan,
        );

        return view('web.dashboard.subscription.index', [
            'plans' => $plans,
            'currentPlan' => $currentPlan,
            'subscription' => $this->subscriptionService->snapshot($user),
            'usage' => $this->aiUsageService->snapshot($user),
        ]);
    }

    public function billing(Request $request): View
    {
        return view('web.dashboard.billing.index', [
            'payments' => $this->paymentService->historyForUser($request->user()),
            'pdfExports' => $this->pdfExportService->historyForUser($request->user()),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('web.dashboard.profile.index', [
            'user' => $request->user(),
        ]);
    }
}
