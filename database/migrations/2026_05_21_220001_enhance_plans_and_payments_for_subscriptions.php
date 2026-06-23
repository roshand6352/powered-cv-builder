<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('currency', 3)->default('ZAR')->after('price');
            $table->unsignedInteger('duration_hours')->nullable()->after('billing_interval');
            $table->unsignedInteger('duration_days')->nullable()->after('duration_hours');
            $table->boolean('is_lifetime')->default(false)->after('duration_days');
            $table->boolean('has_watermark')->default(true)->after('is_lifetime');
            $table->boolean('is_featured')->default(false)->after('has_watermark');
            $table->unsignedInteger('sort_order')->default(0)->after('is_featured');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('reference')->nullable()->after('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn('reference');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'currency',
                'duration_hours',
                'duration_days',
                'is_lifetime',
                'has_watermark',
                'is_featured',
                'sort_order',
            ]);
        });
    }
};
