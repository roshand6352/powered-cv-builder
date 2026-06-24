@include('web.landing.partials.pricing-plans', [
    'plans' => $plans ?? collect(),
    'currentPlan' => $currentPlan ?? null,
    'showAllPlansButton' => $hasMorePlans ?? false,
])
