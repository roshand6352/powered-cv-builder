<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\GoogleOAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function __construct(
        protected GoogleOAuthService $googleOAuthService,
    ) {}

    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirect(): RedirectResponse
    {
        if (! $this->googleOAuthIsConfigured()) {
            return $this->configurationErrorRedirect();
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google after authentication.
     */
    public function callback(): RedirectResponse
    {
        if (! $this->googleOAuthIsConfigured()) {
            return $this->configurationErrorRedirect();
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            Log::warning('Google OAuth callback failed.', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __('Unable to sign in with Google. Please try again or use email and password.'),
                ]);
        }

        $email = trim((string) $googleUser->getEmail());

        if ($email === '') {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __('Google did not provide an email address for your account. Please use email and password instead.'),
                ]);
        }

        try {
            $user = $this->googleOAuthService->resolveUser($googleUser);
        } catch (Throwable $exception) {
            Log::error('Google OAuth user resolution failed.', [
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __('We could not complete your Google sign-in. Please try again or contact support.'),
                ]);
        }

        Auth::login($user, remember: true);

        request()->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    protected function googleOAuthIsConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'));
    }

    protected function configurationErrorRedirect(): RedirectResponse
    {
        return redirect()
            ->route('login')
            ->withErrors([
                'email' => __('Google sign-in is not available right now. Please use email and password.'),
            ]);
    }
}
