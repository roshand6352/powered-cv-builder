<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->unsignedInteger('sort_order')->default(0)->after('status');
            $table->boolean('is_premium')->default(false)->after('sort_order');

            $table->index(['type', 'status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropIndex(['type', 'status', 'sort_order']);
            $table->dropColumn(['description', 'sort_order', 'is_premium']);
        });
    }
};
