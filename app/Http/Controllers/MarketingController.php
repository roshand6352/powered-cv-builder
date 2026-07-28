<?php

namespace App\Http\Controllers;

use App\Models\CvTemplate;
use App\Services\Subscription\PlanService;
use App\Services\Subscription\SubscriptionService;
use App\Support\Cache\ApplicationCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function __construct(
        protected PlanService $planService,
        protected SubscriptionService $subscriptionService,
    ) {}
    public function home(): View
    {
        $plans = $this->planService->websitePlans();

        return view('web.landing.home', [
            'plans' => $plans->take(3),
            'hasMorePlans' => $plans->count() > 3,
            'seo' => [
                'title' => 'Free CV Builder for South Africa',
                'description' => 'Build a professional, ATS-ready CV with ApplyDocs ZA. Use guided prompts, free downloads, and AI-powered writing for South African job seekers.',
            ],
        ]);
    }

    public function templates(): View
    {
        return view('web.landing.templates', [
            'templates' => Cache::remember(
                ApplicationCache::MARKETING_CV_TEMPLATES,
                now()->addMinutes(10),
                fn () => CvTemplate::query()
                    ->cvOnly()
                    ->active()
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug', 'description', 'layout_json']),
            ),
            'seo' => [
                'title' => 'CV Templates',
                'description' => 'Browse professional, ATS-friendly CV templates. Pick a design and start building your resume in minutes.',
            ],
        ]);
    }

    public function pricing(): View
    {
        $currentPlan = null;
        $plans = $this->planService->websitePlans();

        if (auth()->check()) {
            $currentPlan = $this->subscriptionService->resolveActivePlan(auth()->user());
            $plans = $this->planService->withCurrentPlan($plans, $currentPlan);
        }

        return view('web.landing.pricing', [
            'plans' => $plans,
            'currentPlan' => $currentPlan,
            'seo' => [
                'title' => 'Pricing',
                'description' => 'Simple, transparent pricing. Start free or upgrade with PayFast for premium features.',
            ],
        ]);
    }

    public function contact(): View
    {
        return view('web.landing.contact', [
            'seo' => [
                'title' => 'Contact Us',
                'description' => 'Get in touch with our team. We are here to help with questions about CV building, billing, and enterprise plans.',
            ],
        ]);
    }

    public function about(): View
    {
        return view('web.landing.about', [
            'seo' => [
                'title' => 'About Us',
                'description' => 'We help job seekers worldwide create compelling CVs with AI-powered tools and recruiter-approved templates.',
            ],
        ]);
    }

    public function privacy(): View
    {
        return view('web.landing.privacy-policy', [
            'seo' => [
                'title' => 'Privacy Policy',
                'description' => 'Learn how ApplyDocs ZA collects, uses, and protects your personal information.',
            ],
        ]);
    }

    public function terms(): View
    {
        return view('web.landing.terms', [
            'seo' => [
                'title' => 'Terms of Service',
                'description' => 'Terms and conditions for using the ApplyDocs ZA platform and services.',
            ],
        ]);
    }
}
