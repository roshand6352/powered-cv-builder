<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_logs', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('feature');
            $table->text('result')->nullable()->after('response');
            $table->text('error_message')->nullable()->after('result');
            $table->timestamp('completed_at')->nullable()->after('error_message');

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('ai_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropColumn(['status', 'result', 'error_message', 'completed_at']);
        });
    }
};
