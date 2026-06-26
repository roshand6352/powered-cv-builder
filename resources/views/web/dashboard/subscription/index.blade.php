<x-dashboard.layout title="Subscription">
    <x-slot name="header">
        <x-dashboard.page-header
            title="Subscription"
            description="Manage your plan, upgrade, downgrade, and unlock premium features."
            class="mb-0"
        />
    </x-slot>

    @if (session('success'))
        <div class="dash-alert-success">{{ session('success') }}</div>
    @endif

    @if (session('info'))
        <div class="dash-alert-info">{{ session('info') }}</div>
    @endif

    @if (session('error'))
        <div class="dash-alert-error">{{ session('error') }}</div>
    @endif

    <div class="dash-card mb-6 p-5 sm:mb-8 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-start sm:justify-between">
            <div>
                <span class="dash-badge-brand mb-2 w-fit">Current Plan</span>
                <p class="mt-1 text-2xl font-bold text-navy">{{ $subscription['plan']['name'] }}</p>
                @if (! empty($subscription['expires_at']) && ! ($subscription['plan']['is_lifetime'] ?? false) && ! ($subscription['plan']['is_free'] ?? false))
                    <p class="mt-2 text-sm text-muted">Expires on: {{ \Illuminate\Support\Carbon::parse($subscription['expires_at'])->format('d M Y') }}</p>
                @endif
                <p class="mt-2 text-sm text-muted">
                    AI credits: {{ $usage['used'] }} / {{ $usage['limit'] }}
                    @if ($subscription['has_watermark'])
                        · PDF watermark enabled
                    @else
                        · No PDF watermark
                    @endif
                </p>
            </div>

            <div class="text-left sm:text-right">
                @if ($subscription['plan']['is_lifetime'])
                    <span class="dash-badge-brand">Lifetime</span>
                @elseif (empty($subscription['expires_at']) && ! ($subscription['plan']['is_free'] ?? false))
                    <span class="dash-badge-brand">Active</span>
                @endif
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h2 class="dash-section-title">Available plans</h2>
        <p class="dash-section-desc">Upgrade for more AI credits and watermark-free PDFs. Downgrade to Free anytime.</p>
    </div>

    <x-dashboard.plans-grid
        :plans="$plans"
        :current-plan="$currentPlan"
        :subscription="$subscription"
    />
</x-dashboard.layout>
