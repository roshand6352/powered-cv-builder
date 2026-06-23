<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Subscription\PlanService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use RuntimeException;

class GoogleOAuthService
{
    public function __construct(
        protected PlanService $planService,
    ) {}

    /**
     * Resolve an existing user or create a new account from a Google OAuth profile.
     */
    public function resolveUser(SocialiteUser $googleUser): User
    {
        $googleId = $googleUser->getId();
        $email = Str::lower(trim((string) $googleUser->getEmail()));

        $user = User::query()
            ->where('google_id', $googleId)
            ->first();

        if ($user === null) {
            $user = User::query()
                ->where('email', $email)
                ->first();
        }

        if ($user !== null) {
            return $this->linkExistingUser($user, $googleId, $googleUser);
        }

        if (User::onlyTrashed()->where('email', $email)->exists()) {
            throw new RuntimeException('A previously deleted account exists for this email address.');
        }

        return $this->createUser($googleUser, $googleId, $email);
    }

    protected function linkExistingUser(User $user, string $googleId, SocialiteUser $googleUser): User
    {
        if ($user->google_id !== null && $user->google_id !== $googleId) {
            throw new RuntimeException('This email address is already linked to a different Google account.');
        }

        $attributes = [];

        if ($user->google_id === null) {
            $attributes['google_id'] = $googleId;
        }

        if ($user->email_verified_at === null) {
            $attributes['email_verified_at'] = now();
        }

        if ($attributes !== []) {
            $user->fill($attributes);
            $user->save();
        }

        return $user->fresh();
    }

    protected function createUser(SocialiteUser $googleUser, string $googleId, string $email): User
    {
        $plan = $this->planService->freePlan();

        $user = User::create([
            'name' => $this->resolveName($googleUser),
            'email' => $email,
            'google_id' => $googleId,
            // OAuth-only accounts do not use password login; keep a random hash for DB compatibility.
            'password' => Hash::make(Str::password(32)),
            'plan_id' => $plan->id,
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));

        return $user;
    }

    protected function resolveName(SocialiteUser $googleUser): string
    {
        $name = trim((string) $googleUser->getName());

        if ($name !== '') {
            return $name;
        }

        $nickname = trim((string) $googleUser->getNickname());

        if ($nickname !== '') {
            return $nickname;
        }

        return Str::before((string) $googleUser->getEmail(), '@') ?: 'Google User';
    }
}
