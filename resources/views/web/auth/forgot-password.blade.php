<x-guest-layout>
    <h1>{{ __('Forgot password') }}</h1>
    <p class="auth-sub">{{ __('Enter your email and we will send you a password reset link.') }}</p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="form-row-between">
            <a href="{{ route('login') }}" class="link-orange">&larr; {{ __('Back to login') }}</a>
            <x-primary-button>{{ __('Email Reset Link') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
