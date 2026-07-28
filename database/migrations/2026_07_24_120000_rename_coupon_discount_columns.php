<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('coupons')) {
            return;
        }

        if (Schema::hasColumn('coupons', 'type') && ! Schema::hasColumn('coupons', 'discount_type')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->renameColumn('type', 'discount_type');
            });
        }

        if (Schema::hasColumn('coupons', 'value') && ! Schema::hasColumn('coupons', 'discount_value')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->renameColumn('value', 'discount_value');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('coupons')) {
            return;
        }

        if (Schema::hasColumn('coupons', 'discount_type') && ! Schema::hasColumn('coupons', 'type')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->renameColumn('discount_type', 'type');
            });
        }

        if (Schema::hasColumn('coupons', 'discount_value') && ! Schema::hasColumn('coupons', 'value')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->renameColumn('discount_value', 'value');
            });
        }
    }
};
