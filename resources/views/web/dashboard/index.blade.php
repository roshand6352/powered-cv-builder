<x-dashboard.layout title="Dashboard">
    <x-slot name="header">
        <x-dashboard.page-header
            title="Dashboard"
            description="Welcome back, {{ auth()->user()->name }}. Here's an overview of your account."
            class="mb-0"
        />
    </x-slot>

    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <x-dashboard.stat-card
            label="Total CVs"
            :value="$stats['total_cvs']"
            hint="Resumes in your account"
            icon="document"
            color="brand"
        />

        <x-dashboard.stat-card
            label="Cover Letters"
            :value="$stats['cover_letters']"
            hint="Letters you've created"
            icon="mail"
            color="navy"
        />

        <x-dashboard.stat-card
            label="Plan"
            :value="$stats['plan']"
            hint="Current subscription"
            icon="credit-card"
            color="orange"
        />

        <x-dashboard.stat-card
            label="AI Usage"
            :value="$stats['ai_usage']['used'] . ' / ' . $stats['ai_usage']['limit']"
            :hint="$stats['ai_usage']['label']"
            icon="sparkles"
            color="cream"
        />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="dash-card p-5 sm:p-6">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="dash-section-title text-lg">Quick Actions</h2>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 sm:gap-4">
                    <a href="{{ route('dashboard.cvs') }}" class="group dash-quick-action">
                        <span class="dash-quick-icon">
                            @include('web.components.dashboard.icons.document')
                        </span>
                        <div>
                            <p class="font-medium text-navy">Create CV</p>
                            <p class="text-sm text-muted">Build a new resume</p>
                        </div>
                    </a>
                    <a href="{{ route('dashboard.cover-letters') }}" class="group dash-quick-action">
                        <span class="dash-quick-icon">
                            @include('web.components.dashboard.icons.mail')
                        </span>
                        <div>
                            <p class="font-medium text-navy">Write Cover Letter</p>
                            <p class="text-sm text-muted">Tailor for a job application</p>
                        </div>
                    </a>
                    <a href="{{ route('dashboard.subscription') }}" class="group dash-quick-action">
                        <span class="dash-quick-icon">
                            @include('web.components.dashboard.icons.credit-card')
                        </span>
                        <div>
                            <p class="font-medium text-navy">Upgrade Plan</p>
                            <p class="text-sm text-muted">Unlock more AI credits</p>
                        </div>
                    </a>
                    <a href="{{ route('dashboard.profile') }}" class="group dash-quick-action">
                        <span class="dash-quick-icon">
                            @include('web.components.dashboard.icons.user')
                        </span>
                        <div>
                            <p class="font-medium text-navy">Edit Profile</p>
                            <p class="text-sm text-muted">Update your account details</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="dash-card p-5 sm:p-6">
            <h2 class="mb-6 dash-section-title text-lg">Recent Activity</h2>
            <ul class="space-y-4">
                @forelse ($recentActivity as $activity)
                    <li class="flex items-start gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                            @include('web.components.dashboard.icons.' . $activity['icon'])
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-navy">{{ $activity['action'] }}</p>
                            <p class="text-xs text-muted">{{ $activity['time'] }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-muted">No recent activity yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-dashboard.layout>
