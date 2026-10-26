<div class="sticky top-0 z-30 border-b border-line bg-white/95 backdrop-blur-sm">
    <header class="flex min-h-16 flex-wrap items-center justify-between gap-x-3 gap-y-2 px-3 py-3 sm:px-4 lg:px-8">
        <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
            <button
                type="button"
                class="inline-flex shrink-0 items-center justify-center rounded-lg p-2 text-body hover:bg-cream lg:hidden"
                @click="sidebarOpen = true"
                aria-label="Open sidebar"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            @if (isset($header) && ! $header->isEmpty())
                <div class="dash-navbar-heading hidden min-w-0 sm:block">
                    {{ $header }}
                </div>
            @endif
        </div>

        <div class="flex shrink-0 items-center gap-2 sm:gap-3">
            @unless (request()->routeIs(
                'dashboard',
                'dashboard.cvs',
                'dashboard.cover-letters',
                'dashboard.profile',
                'dashboard.billing',
                'dashboard.subscription',
                'job-match',
                'job-match.*',
                'cvs.cover-letters.*',
                'cover-letters.*',
                'profile.edit',
            ))
                <a
                    href="{{ route('dashboard.cvs') }}"
                    class="dash-btn-primary p-2.5 sm:hidden"
                    aria-label="New CV"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>

                <a href="{{ route('dashboard.cvs') }}" class="dash-btn-primary hidden sm:inline-flex">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New CV
                </a>
            @endunless

            <x-marketing.user-dropdown />
        </div>
    </header>

    @if (isset($header) && ! $header->isEmpty())
        <div class="border-t border-line bg-white px-3 py-4 sm:hidden">
            {{ $header }}
        </div>
    @endif
</div>
