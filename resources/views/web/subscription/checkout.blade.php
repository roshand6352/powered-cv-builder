<x-dashboard.layout title="Checkout">
    <x-slot name="header">
        <x-dashboard.page-header
            title="Checkout"
            description="Review your plan and continue to secure payment with PayFast."
            class="mb-0"
        />
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        @if (session('error'))
            <div class="dash-alert-error">{{ session('error') }}</div>
        @endif

        <div class="dash-card p-6">
            <h2 class="text-lg font-semibold text-navy">Plan details</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-xl bg-brand-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-muted">Selected plan</p>
                    <p class="mt-1 text-xl font-bold text-navy">{{ $plan->name }}</p>
                    <p class="mt-1 text-sm text-muted">{{ $plan->description }}</p>
                </div>
                <div class="rounded-xl bg-brand-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-muted">Amount</p>
                    <p class="mt-1 text-xl font-bold text-navy">{{ $plan->formattedPrice() }}</p>
                    <p class="mt-1 text-sm text-muted">{{ $plan->displayPriceSuffix() ?? '/ one-time' }}</p>
                </div>
            </div>
        </div>

        <div class="dash-card p-6">
            <h2 class="text-lg font-semibold text-navy">Account</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs uppercase tracking-wide text-muted">Name</p>
                    <p class="mt-1 font-semibold text-navy">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-muted">Email</p>
                    <p class="mt-1 font-semibold text-navy">{{ $user->email }}</p>
                </div>
            </div>
            <div class="mt-6 flex flex-wrap gap-3">
                <form method="POST" action="{{ route('subscription.pay', $plan) }}">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="plan_name" value="{{ $plan->name }}">
                    <input type="hidden" name="plan_price" value="{{ $plan->price }}">
                    <button type="submit" class="dash-btn-primary">
                        Pay now with PayFast
                    </button>
                </form>
                <a href="{{ route('pricing') }}" class="dash-btn-secondary">
                    Back to pricing
                </a>
            </div>
        </div>
    </div>
</x-dashboard.layout>
