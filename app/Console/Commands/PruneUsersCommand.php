<?php

namespace App\Console\Commands;

use App\Enums\PlanSlug;
use App\Enums\RoleName;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class PruneUsersCommand extends Command
{
    protected $signature = 'users:prune {--force : Run without confirmation}';

    protected $description = 'Keep only Admin and John Doe users; remove all others and related data';

    /**
     * @var list<string>
     */
    protected array $keepEmails = [
        'admin@example.com',
        'john@example.com',
    ];

    /**
     * @var list<string>
     */
    protected array $trackedTables = [
        'users',
        'cvs',
        'cv_sections',
        'cover_letters',
        'subscriptions',
        'payments',
        'ai_logs',
        'activities',
        'activity_log',
        'pdf_exports',
        'media',
        'model_has_roles',
        'personal_access_tokens',
        'sessions',
        'notifications',
    ];

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('This will delete all users except Admin and John Doe. Continue?')) {
            $this->warn('Aborted.');

            return self::FAILURE;
        }

        $before = $this->snapshotCounts();

        DB::transaction(function (): void {
            $this->ensureKeptUsers();

            $keepIds = User::withTrashed()
                ->whereIn('email', $this->keepEmails)
                ->pluck('id')
                ->all();

            $usersToRemove = User::withTrashed()
                ->whereNotIn('email', $this->keepEmails)
                ->get();

            $removeIds = $usersToRemove->pluck('id')->all();
            $removeEmails = $usersToRemove->pluck('email')->all();

            if ($removeIds !== []) {
                $this->purgeRelatedUserData($removeIds, $removeEmails);

                foreach ($usersToRemove as $user) {
                    $user->forceDelete();
                }
            }

            $this->purgeOrphanedUserReferences($keepIds);
        });

        $after = $this->snapshotCounts();
        $this->renderReport($before, $after);

        return self::SUCCESS;
    }

    protected function ensureKeptUsers(): void
    {
        $freePlan = Plan::query()->where('slug', PlanSlug::Free->value)->first();

        $admin = User::withTrashed()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        if ($admin->trashed()) {
            $admin->restore();
        }

        $admin->update([
            'name' => 'Admin',
            'email_verified_at' => $admin->email_verified_at ?? now(),
        ]);
        $admin->syncRoles([RoleName::Admin->value]);

        $john = User::withTrashed()->firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'plan_id' => $freePlan?->id,
            ],
        );

        if ($john->trashed()) {
            $john->restore();
        }

        $john->update([
            'name' => 'John Doe',
            'email_verified_at' => $john->email_verified_at ?? now(),
            'plan_id' => $john->plan_id ?? $freePlan?->id,
        ]);
        $john->syncRoles(['user']);
    }

    /**
     * @param  list<int>  $removeIds
     * @param  list<string>  $removeEmails
     */
    protected function purgeRelatedUserData(array $removeIds, array $removeEmails): void
    {
        $userClass = User::class;

        if (Schema::hasTable('pdf_exports')) {
            DB::table('pdf_exports')->whereIn('user_id', $removeIds)->delete();
        }

        if (Schema::hasTable('cover_letters')) {
            DB::table('cover_letters')->whereIn('user_id', $removeIds)->delete();
        }

        if (Schema::hasTable('cv_sections') && Schema::hasTable('cvs')) {
            $cvIds = DB::table('cvs')->whereIn('user_id', $removeIds)->pluck('id');

            if ($cvIds->isNotEmpty()) {
                DB::table('cv_sections')->whereIn('cv_id', $cvIds)->delete();
            }
        }

        if (Schema::hasTable('cvs')) {
            DB::table('cvs')->whereIn('user_id', $removeIds)->delete();
        }

        if (Schema::hasTable('payments')) {
            DB::table('payments')->whereIn('user_id', $removeIds)->delete();
        }

        if (Schema::hasTable('subscriptions')) {
            DB::table('subscriptions')->whereIn('user_id', $removeIds)->delete();
        }

        if (Schema::hasTable('ai_logs')) {
            DB::table('ai_logs')->whereIn('user_id', $removeIds)->delete();
        }

        if (Schema::hasTable('activities')) {
            DB::table('activities')->whereIn('user_id', $removeIds)->delete();
        }

        if (Schema::hasTable('activity_log')) {
            DB::table('activity_log')
                ->where(function ($query) use ($userClass, $removeIds): void {
                    $query->where(function ($inner) use ($userClass, $removeIds): void {
                        $inner->where('subject_type', $userClass)
                            ->whereIn('subject_id', $removeIds);
                    })->orWhere(function ($inner) use ($userClass, $removeIds): void {
                        $inner->where('causer_type', $userClass)
                            ->whereIn('causer_id', $removeIds);
                    });
                })
                ->delete();
        }

        if (Schema::hasTable('media')) {
            DB::table('media')
                ->where('model_type', $userClass)
                ->whereIn('model_id', $removeIds)
                ->delete();
        }

        if (Schema::hasTable('model_has_roles')) {
            DB::table('model_has_roles')
                ->where('model_type', $userClass)
                ->whereIn('model_id', $removeIds)
                ->delete();
        }

        if (Schema::hasTable('model_has_permissions')) {
            DB::table('model_has_permissions')
                ->where('model_type', $userClass)
                ->whereIn('model_id', $removeIds)
                ->delete();
        }

        if (Schema::hasTable('personal_access_tokens')) {
            DB::table('personal_access_tokens')
                ->where('tokenable_type', $userClass)
                ->whereIn('tokenable_id', $removeIds)
                ->delete();
        }

        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->whereIn('user_id', $removeIds)->delete();
        }

        if ($removeEmails !== [] && Schema::hasTable('password_reset_tokens')) {
            DB::table('password_reset_tokens')->whereIn('email', $removeEmails)->delete();
        }

        if (Schema::hasTable('notifications')) {
            DB::table('notifications')
                ->where('notifiable_type', $userClass)
                ->whereIn('notifiable_id', $removeIds)
                ->delete();
        }
    }

    /**
     * @param  list<int>  $keepIds
     */
    protected function purgeOrphanedUserReferences(array $keepIds): void
    {
        $userClass = User::class;
        $validIds = User::query()->pluck('id')->all();

        if (Schema::hasTable('activity_log')) {
            DB::table('activity_log')
                ->where('subject_type', $userClass)
                ->whereNotIn('subject_id', $validIds)
                ->delete();

            DB::table('activity_log')
                ->where('causer_type', $userClass)
                ->whereNotNull('causer_id')
                ->whereNotIn('causer_id', $validIds)
                ->delete();
        }

        if (Schema::hasTable('media')) {
            DB::table('media')
                ->where('model_type', $userClass)
                ->whereNotIn('model_id', $validIds)
                ->delete();
        }

        if (Schema::hasTable('model_has_roles')) {
            DB::table('model_has_roles')
                ->where('model_type', $userClass)
                ->whereNotIn('model_id', $validIds)
                ->delete();
        }

        if (Schema::hasTable('notifications')) {
            DB::table('notifications')
                ->where('notifiable_type', $userClass)
                ->whereNotIn('notifiable_id', $validIds)
                ->delete();
        }
    }

    /**
     * @return array<string, int|string>
     */
    protected function snapshotCounts(): array
    {
        $counts = [];

        foreach ($this->trackedTables as $table) {
            if (! Schema::hasTable($table)) {
                $counts[$table] = 'n/a';

                continue;
            }

            $counts[$table] = DB::table($table)->count();
        }

        $counts['cv_data_sections'] = $this->countCvDataSections();

        return $counts;
    }

    protected function countCvDataSections(): int
    {
        if (! Schema::hasTable('cvs')) {
            return 0;
        }

        $total = 0;

        DB::table('cvs')
            ->select('cv_data')
            ->orderBy('id')
            ->chunk(100, function ($rows) use (&$total): void {
                foreach ($rows as $row) {
                    $data = json_decode($row->cv_data ?? 'null', true);

                    if (! is_array($data)) {
                        continue;
                    }

                    foreach (['experience', 'education', 'skills', 'social_links'] as $section) {
                        if (! empty($data[$section]) && is_array($data[$section])) {
                            $total += count($data[$section]);
                        }
                    }

                    if (! empty($data['profile']) && is_array($data['profile'])) {
                        $total++;
                    }
                }
            });

        return $total;
    }

    /**
     * @param  array<string, int|string>  $before
     * @param  array<string, int|string>  $after
     */
    protected function renderReport(array $before, array $after): void
    {
        $this->newLine();
        $this->info('User cleanup report');
        $this->line(str_repeat('-', 72));

        $rows = [];

        foreach ($this->trackedTables as $table) {
            $rows[] = [
                $table,
                (string) ($before[$table] ?? 'n/a'),
                (string) ($after[$table] ?? 'n/a'),
                $this->deltaLabel($before[$table] ?? 0, $after[$table] ?? 0),
            ];
        }

        $rows[] = [
            'cv_data (profile/experience/education/skills/social)',
            (string) ($before['cv_data_sections'] ?? 0),
            (string) ($after['cv_data_sections'] ?? 0),
            $this->deltaLabel($before['cv_data_sections'] ?? 0, $after['cv_data_sections'] ?? 0),
        ];

        $this->table(['Table / data', 'Before', 'After', 'Change'], $rows);

        $this->newLine();
        $this->info('Remaining users:');

        foreach (User::query()->orderBy('id')->get(['id', 'name', 'email']) as $user) {
            $roles = $user->getRoleNames()->implode(', ');
            $this->line("  [{$user->id}] {$user->name} <{$user->email}> — {$roles}");
        }
    }

    protected function deltaLabel(int|string $before, int|string $after): string
    {
        if (! is_numeric($before) || ! is_numeric($after)) {
            return '—';
        }

        $delta = (int) $after - (int) $before;

        if ($delta === 0) {
            return '0';
        }

        return $delta > 0 ? "+{$delta}" : (string) $delta;
    }
}
