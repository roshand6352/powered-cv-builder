<x-guest-layout>
    <h1>{{ __('Verify your email') }}</h1>
    <p class="auth-sub">{{ __('Thanks for signing up! Please verify your email address by clicking the link we sent you.') }}</p>

    @if (session('status') == 'verification-link-sent')
        <div class="form-status">{{ __('A new verification link has been sent to your email address.') }}</div>
    @endif

    <div class="form-row-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>{{ __('Resend Verification Email') }}</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="link-orange" style="background:none;border:none;cursor:pointer;font:inherit;padding:0">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
