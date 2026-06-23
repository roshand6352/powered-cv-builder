<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_templates', function (Blueprint $table) {
            $table->string('type', 32)
                ->default('cv')
                ->after('slug')
                ->index();
        });

        DB::table('cv_templates')->update(['type' => 'cv']);
    }

    public function down(): void
    {
        Schema::table('cv_templates', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
