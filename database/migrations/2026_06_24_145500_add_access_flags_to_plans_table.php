<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table): void {
            if (! Schema::hasColumn('plans', 'duration_type')) {
                $table->string('duration_type', 20)->nullable()->after('currency');
            }

            if (! Schema::hasColumn('plans', 'duration_value')) {
                $table->unsignedInteger('duration_value')->nullable()->after('duration_type');
            }

            if (! Schema::hasColumn('plans', 'watermark_enabled')) {
                $table->boolean('watermark_enabled')->nullable()->after('duration_value');
            }

            if (! Schema::hasColumn('plans', 'ai_enabled')) {
                $table->boolean('ai_enabled')->default(false)->after('watermark_enabled');
            }

            if (! Schema::hasColumn('plans', 'cover_letter_enabled')) {
                $table->boolean('cover_letter_enabled')->default(false)->after('ai_enabled');
            }
        });

        DB::table('plans')->orderBy('id')->each(function (object $plan): void {
            $updates = [];

            if (property_exists($plan, 'watermark_enabled') && $plan->watermark_enabled === null && property_exists($plan, 'has_watermark')) {
                $updates['watermark_enabled'] = (bool) $plan->has_watermark;
            }

            if (property_exists($plan, 'duration_type') && blank($plan->duration_type)) {
                if (property_exists($plan, 'is_lifetime') && $plan->is_lifetime) {
                    $updates['duration_type'] = 'unlimited';
                    $updates['duration_value'] = null;
                } elseif (property_exists($plan, 'duration_days') && $plan->duration_days) {
                    $days = (int) $plan->duration_days;

                    if ($days === 7) {
                        $updates['duration_type'] = 'week';
                        $updates['duration_value'] = 1;
                    } elseif ($days === 30) {
                        $updates['duration_type'] = 'month';
                        $updates['duration_value'] = 1;
                    } elseif ($days === 365) {
                        $updates['duration_type'] = 'year';
                        $updates['duration_value'] = 1;
                    } else {
                        $updates['duration_type'] = 'day';
                        $updates['duration_value'] = $days;
                    }
                } elseif (property_exists($plan, 'duration_hours') && $plan->duration_hours) {
                    $hours = (int) $plan->duration_hours;
                    $updates['duration_type'] = 'day';
                    $updates['duration_value'] = max(1, (int) ceil($hours / 24));
                }
            }

            if ($updates !== []) {
                DB::table('plans')->where('id', $plan->id)->update($updates);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table): void {
            $columns = [];

            foreach (['duration_type', 'duration_value', 'watermark_enabled', 'ai_enabled', 'cover_letter_enabled'] as $column) {
                if (Schema::hasColumn('plans', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
