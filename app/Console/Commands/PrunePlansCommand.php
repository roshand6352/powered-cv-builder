<?php

namespace App\Console\Commands;

use App\Enums\PlanSlug;
use App\Models\Plan;
use App\Models\User;
use App\Support\Cache\ApplicationCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PrunePlansCommand extends Command
{
    protected $signature = 'plans:prune {--force : Run without confirmation}';

    protected $description = 'Keep only the seeded catalog plans and remove all others';

    /**
     * @var list<string>
     */
    protected array $keepSlugs = [
        'free',
        'daily',
        'weekly',
        'monthly',
        'yearly',
    ];

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('This will delete all plans except the seeded catalog plans. Continue?')) {
            $this->warn('Aborted.');

            return self::FAILURE;
        }

        $before = $this->snapshot();

        DB::transaction(function (): void {
            $this->callSilent('db:seed', ['--class' => 'Database\\Seeders\\PlanSeeder']);

            $freePlan = Plan::query()->where('slug', PlanSlug::Free->value)->firstOrFail();
            $removeIds = Plan::withTrashed()
                ->whereNotIn('slug', $this->keepSlugs)
                ->pluck('id')
                ->all();

            if ($removeIds === []) {
                return;
            }

            User::withTrashed()
                ->whereIn('plan_id', $removeIds)
                ->update([
                    'plan_id' => $freePlan->id,
                    'plan_expiry' => null,
                ]);

            if (Schema::hasTable('subscriptions')) {
                DB::table('subscriptions')
                    ->whereIn('plan_id', $removeIds)
                    ->update(['plan_id' => $freePlan->id]);
            }

            Plan::withTrashed()
                ->whereIn('id', $removeIds)
                ->forceDelete();
        });

        ApplicationCache::forgetPlans();
        Cache::forget(ApplicationCache::PLANS_PRO);

        $after = $this->snapshot();
        $this->renderReport($before, $after);

        return self::SUCCESS;
    }

    /**
     * @return array{plans: list<string>, users: list<string>, subscriptions: int, payments: int}
     */
    protected function snapshot(): array
    {
        $plans = Plan::withTrashed()
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug'])
            ->map(fn (Plan $plan): string => "{$plan->name} ({$plan->slug})")
            ->all();

        $users = User::query()
            ->with('plan:id,name,slug')
            ->orderBy('id')
            ->get(['id', 'name', 'email', 'plan_id'])
            ->map(fn (User $user): string => "{$user->name} <{$user->email}> — ".($user->plan?->name ?? 'no plan'))
            ->all();

        return [
            'plans' => $plans,
            'users' => $users,
            'subscriptions' => Schema::hasTable('subscriptions') ? DB::table('subscriptions')->count() : 0,
            'payments' => Schema::hasTable('payments') ? DB::table('payments')->count() : 0,
        ];
    }

    /**
     * @param  array{plans: list<string>, users: list<string>, subscriptions: int, payments: int}  $before
     * @param  array{plans: list<string>, users: list<string>, subscriptions: int, payments: int}  $after
     */
    protected function renderReport(array $before, array $after): void
    {
        $this->newLine();
        $this->info('Plan cleanup report');
        $this->line(str_repeat('-', 72));

        $this->line('Plans before ('.count($before['plans']).'):');
        foreach ($before['plans'] as $plan) {
            $this->line("  - {$plan}");
        }

        $this->newLine();
        $this->line('Plans after ('.count($after['plans']).'):');
        foreach ($after['plans'] as $plan) {
            $this->line("  - {$plan}");
        }

        $this->newLine();
        $this->line('Subscriptions: '.$before['subscriptions'].' → '.$after['subscriptions']);
        $this->line('Payments: '.$before['payments'].' → '.$after['payments'].' (historical plan references may be nulled)');

        $this->newLine();
        $this->info('User plan assignments:');
        foreach ($after['users'] as $user) {
            $this->line("  - {$user}");
        }
    }
}
