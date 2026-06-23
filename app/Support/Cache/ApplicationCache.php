<?php

namespace App\Support\Cache;

use App\Models\AiLog;
use App\Models\CvTemplate;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

final class ApplicationCache
{
    public const PLANS_ACTIVE = 'plans.active';

    public const PLANS_WEBSITE = 'plans.website';

    public const PLANS_FREE = 'plans.free';

    public const PLANS_PRO = 'plans.pro';

    public const CV_TEMPLATES_CATALOG = 'cv_templates.catalog';

    public const CV_TEMPLATES_BUILDER = 'cv_templates.builder';

    public const MARKETING_CV_TEMPLATES = 'marketing.cv_templates';

    public const FILAMENT_STATS_OVERVIEW = 'filament.dashboard.stats_overview';

    public static function forgetPlans(): void
    {
        foreach ([self::PLANS_ACTIVE, self::PLANS_WEBSITE, self::PLANS_FREE, self::PLANS_PRO] as $key) {
            Cache::forget($key);
        }
    }

    public static function forgetCvTemplates(): void
    {
        foreach ([self::CV_TEMPLATES_CATALOG, self::CV_TEMPLATES_BUILDER, self::MARKETING_CV_TEMPLATES] as $key) {
            Cache::forget($key);
        }
    }

    public static function forgetFilamentDashboardStats(): void
    {
        Cache::forget(self::FILAMENT_STATS_OVERVIEW);
    }

    public static function registerInvalidation(): void
    {
        Plan::saved(fn () => self::forgetPlans());
        Plan::deleted(fn () => self::forgetPlans());

        CvTemplate::saved(fn () => self::forgetCvTemplates());
        CvTemplate::deleted(fn () => self::forgetCvTemplates());

        foreach ([User::class, Payment::class, Subscription::class, AiLog::class] as $model) {
            $model::saved(fn () => self::forgetFilamentDashboardStats());
            $model::deleted(fn () => self::forgetFilamentDashboardStats());
        }
    }
}
