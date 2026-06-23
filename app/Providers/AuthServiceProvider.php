<?php

namespace App\Providers;

use App\Enums\RoleName;
use App\Models\AiLog;
use App\Models\CmsPage;
use App\Models\CvTemplate;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => \App\Policies\UserPolicy::class,
        Plan::class => \App\Policies\PlanPolicy::class,
        Payment::class => \App\Policies\PaymentPolicy::class,
        Subscription::class => \App\Policies\SubscriptionPolicy::class,
        CvTemplate::class => \App\Policies\CvTemplatePolicy::class,
        AiLog::class => \App\Policies\AiLogPolicy::class,
        CmsPage::class => \App\Policies\CmsPagePolicy::class,
        Setting::class => \App\Policies\SettingPolicy::class,
        \Spatie\Permission\Models\Role::class => \App\Policies\RolePolicy::class,
        \Spatie\Permission\Models\Permission::class => \App\Policies\PermissionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, string $ability) {
            if (! $user instanceof User) {
                return null;
            }

            if ($user->hasRole(RoleName::SuperAdmin->value)) {
                return true;
            }

            return null;
        });
    }
}
