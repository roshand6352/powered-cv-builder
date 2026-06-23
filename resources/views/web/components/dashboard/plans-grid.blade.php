@props([
    'plans',
    'currentPlan',
    'subscription' => null,
])

<div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
    @foreach ($plans as $plan)
        @php
            $isCurrent = $currentPlan->id === $plan->id && ! ($subscription['is_expired'] ?? false);
            $action = $plan->sort_order > $currentPlan->sort_order
                ? 'upgrade'
                : ($plan->sort_order < $currentPlan->sort_order ? 'downgrade' : ($plan->price >= $currentPlan->price ? 'upgrade' : 'downgrade'));
        @endphp

        <div @class([
            'relative flex min-w-0 flex-col dash-card p-5 sm:p-6',
            'border-brand-500 ring-2 ring-brand-200' => $isCurrent,
        ])>
            @if ($plan->displayBadge())
                <span @class([
                    'absolute -top-3 left-1/2 -translate-x-1/2 rounded-full px-3 py-1 text-xs font-semibold text-white',
                    'bg-brand-600' => blank($plan->accent_color),
                ]) @if ($plan->accent_color) style="background: {{ $plan->accent_color }}" @endif>
                    {{ $plan->displayBadge() }}
                </span>
            @endif

            @if ($isCurrent)
                <span class="dash-badge-brand mb-3 w-fit">Current plan</span>
            @endif

            <h3 class="text-lg font-semibold text-navy">{{ $plan->name }}</h3>
            <p class="mt-1 text-sm text-muted">{{ $plan->description }}</p>

            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-3xl font-bold text-navy">{{ $plan->formattedPrice() }}</span>
                @if (! $plan->isFree() && ! $plan->isLifetime())
                    <span class="text-sm text-muted">/ {{ $plan->periodLabel() }}</span>
                @endif
            </div>

            <ul class="mt-6 flex-1 space-y-2">
                @foreach ($plan->displayFeatures() as $feature)
                    <li @class([
                        'flex items-start gap-2 text-sm',
                        'text-body' => $feature['status'] === 'active',
                        'text-muted opacity-60' => $feature['status'] === 'disabled',
                    ])>
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        {{ $feature['title'] }}
                    </li>
                @endforeach
            </ul>

            <div class="mt-6">
                @if ($isCurrent)
                    <button type="button" disabled class="w-full cursor-not-allowed rounded-lg border border-line px-4 py-2.5 text-sm font-semibold text-muted">
                        Current plan
                    </button>
                @elseif ($action === 'downgrade' && $plan->isFree())
                    <form method="POST" action="{{ route('subscription.pay', $plan) }}">
                        @csrf
                        <button type="submit" class="dash-btn-secondary w-full">
                            Downgrade to Free
                        </button>
                    </form>
                @else
                    <form method="GET" action="{{ route('subscription.checkout', $plan) }}">
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <input type="hidden" name="plan_name" value="{{ $plan->name }}">
                        <input type="hidden" name="plan_price" value="{{ $plan->price }}">
                        <button
                            type="submit"
                            @class([
                                'w-full rounded-lg px-4 py-2.5 text-sm font-semibold transition',
                                'bg-brand-600 text-white hover:bg-brand-700' => $plan->is_featured || $action === 'upgrade',
                                'border border-brand-300 text-brand-700 hover:bg-brand-50' => ! $plan->is_featured && $action !== 'upgrade',
                            ])
                        >
                            {{ $action === 'upgrade' ? 'Upgrade' : 'Choose plan' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
</div>
