@props(['align' => 'right'])

@auth
<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button
        type="button"
        @click="open = !open"
        class="inline-flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-navy transition hover:bg-cream focus:outline-none focus:ring-2 focus:ring-brand-500"
        aria-haspopup="true"
        :aria-expanded="open"
    >
        <x-user-avatar :user="auth()->user()" size="sm" />
        <span class="hidden max-w-[120px] truncate sm:inline">{{ auth()->user()->name }}</span>
        <svg class="h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        @class([
            'absolute z-[60] mt-2 w-56 max-w-[calc(100vw-1.5rem)] rounded-lg border border-line bg-white py-1 shadow-lg sm:w-48',
            'right-0' => $align === 'right',
            'left-0' => $align === 'left',
        ])
    >
        <div class="border-b border-line px-4 py-3">
            <p class="truncate text-sm font-medium text-navy">{{ auth()->user()->name }}</p>
            <p class="truncate text-xs text-muted">{{ auth()->user()->email }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-body hover:bg-cream hover:text-navy">Dashboard</a>
        <a href="{{ route('dashboard.profile') }}" class="block px-4 py-2 text-sm text-body hover:bg-cream hover:text-navy">Profile Settings</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-body hover:bg-cream hover:text-navy">
                Log Out
            </button>
        </form>
    </div>
</div>
@endauth
