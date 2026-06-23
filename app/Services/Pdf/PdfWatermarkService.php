<?php

namespace App\Services\Pdf;

use App\Enums\PlanSlug;
use App\Models\Plan;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Carbon;

class PdfWatermarkService extends BaseService
{
    public function shouldApply(?User $user): bool
    {
        if (! $user?->id) {
            return false;
        }

        return $this->showWatermarkForUser($user);
    }

    public function shouldApplyForUserId(int $userId): bool
    {
        $user = User::query()->find($userId);

        if (! $user) {
            return false;
        }

        return $this->showWatermarkForUser($user);
    }

    /**
     * Resolve watermark visibility from the user's plan price at PDF generation time.
     */
    public function showWatermarkForUser(User $user): bool
    {
        if (! $user->plan_id) {
            return true;
        }

        $plan = $user->relationLoaded('plan')
            ? $user->plan
            : Plan::query()->find($user->plan_id);

        if (! $plan) {
            return true;
        }

        if ($this->paidPlanIsExpired((float) $plan->price, $plan->slug, (bool) $plan->is_lifetime, $user->plan_expiry)) {
            return true;
        }

        return (float) $plan->price == 0.0;
    }

    public function primaryText(): string
    {
        return (string) config('cv-templates.watermark.primary_text', 'FREE VERSION');
    }

    public function secondaryText(): string
    {
        return (string) config('cv-templates.watermark.secondary_text', 'Generated with Free Plan');
    }

    public function textOpacity(): float
    {
        return (float) config('cv-templates.watermark.text_opacity', 0.14);
    }

    public function logoOpacity(): float
    {
        return (float) config('cv-templates.watermark.logo_opacity', 0.12);
    }

    public function angle(): float
    {
        return (float) config('cv-templates.watermark.angle', -35);
    }

    public function shouldTryLogo(): bool
    {
        return (bool) config('cv-templates.watermark.use_logo', true);
    }

    public function logoPath(): string
    {
        return public_path((string) config(
            'cv-templates.watermark.logo',
            config('marketing.brand.logo', 'images/LOGO-02.png'),
        ));
    }

    protected function paidPlanIsExpired(
        float $price,
        ?string $slug,
        bool $isLifetime,
        mixed $planExpiry,
    ): bool {
        if ($price <= 0.0 || $isLifetime || $slug === PlanSlug::Free->value) {
            return false;
        }

        if ($planExpiry === null) {
            return false;
        }

        $expiry = $planExpiry instanceof Carbon
            ? $planExpiry
            : Carbon::parse($planExpiry);

        return $expiry->isPast();
    }
}
