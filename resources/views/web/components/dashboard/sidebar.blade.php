<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col border-r border-line bg-white transition-transform duration-200 lg:static lg:z-auto lg:translate-x-0"
>
    <div class="flex h-16 items-center justify-between border-b border-line px-4 lg:px-6">
        <x-marketing.logo class="dash-sidebar-logo" :href="route('dashboard')" />

        <button
            type="button"
            class="rounded-lg p-1.5 text-muted hover:bg-cream hover:text-navy lg:hidden"
            @click="sidebarOpen = false"
            aria-label="Close sidebar"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto p-4" aria-label="Dashboard navigation">
        @foreach (config('dashboard.navigation') as $item)
            <x-dashboard.nav-item
                :href="route($item['route'])"
                :icon="$item['icon']"
                :active="request()->routeIs($item['active'])"
            >
                {{ $item['label'] }}
            </x-dashboard.nav-item>
        @endforeach
    </nav>

    <div class="border-t border-line p-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-body transition hover:bg-cream hover:text-navy">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
            Back to Website
        </a>
    </div>
</aside>
