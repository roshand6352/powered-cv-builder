<x-guest-layout>
    <h1>{{ __('Log in') }}</h1>
    <p class="auth-sub">{{ __('Welcome back. Sign in to your account.') }}</p>

    <x-auth-session-status :status="session('status')" />

    @include('web.components.google-auth-button')

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="form-group">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <label for="remember_me" class="checkbox-row">
            <input id="remember_me" type="checkbox" name="remember">
            <span>{{ __('Remember me') }}</span>
        </label>

        <div class="form-row-between">
            @if (Route::has('password.request'))
                <a class="link-orange" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            @endif
            <x-primary-button>{{ __('Log in') }}</x-primary-button>
        </div>

        @if (Route::has('register'))
            <p class="auth-links">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}">{{ __('Register') }}</a>
            </p>
        @endif
    </form>
</x-guest-layout>
