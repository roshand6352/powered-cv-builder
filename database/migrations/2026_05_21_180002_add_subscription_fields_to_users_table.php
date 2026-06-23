<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('password')->constrained()->nullOnDelete();
            $table->timestamp('plan_expiry')->nullable()->after('plan_id');
            $table->unsignedInteger('ai_usage_count')->default(0)->after('plan_expiry');
            $table->softDeletes();

            $table->index('plan_id');
            $table->index('plan_expiry');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropIndex(['plan_id']);
            $table->dropIndex(['plan_expiry']);
            $table->dropSoftDeletes();
            $table->dropColumn(['plan_id', 'plan_expiry', 'ai_usage_count']);
        });
    }
};
