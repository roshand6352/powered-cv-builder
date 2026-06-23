<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('button_text')->nullable()->after('features');
            $table->string('button_link')->nullable()->after('button_text');
            $table->string('badge_text')->nullable()->after('is_featured');
            $table->string('accent_color', 7)->nullable()->after('badge_text');
            $table->string('button_style', 32)->default('auto')->after('accent_color');
            $table->json('excluded_features')->nullable()->after('button_style');
            $table->text('footer_note')->nullable()->after('excluded_features');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'button_text',
                'button_link',
                'badge_text',
                'accent_color',
                'button_style',
                'excluded_features',
                'footer_note',
            ]);
        });
    }
};
