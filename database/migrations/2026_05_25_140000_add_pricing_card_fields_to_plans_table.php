<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('type_label')->nullable()->after('slug');
            $table->string('currency_symbol', 5)->default('R')->after('currency');
            $table->string('price_suffix')->nullable()->after('currency_symbol');
            $table->boolean('show_badge')->default(false)->after('is_featured');
            $table->boolean('show_payment_methods')->default(false)->after('footer_note');
            $table->json('payment_methods')->nullable()->after('show_payment_methods');
            $table->string('card_border_color', 7)->nullable()->after('accent_color');
            $table->string('card_background_color', 7)->nullable()->after('card_border_color');
            $table->string('button_background_color', 7)->nullable()->after('card_background_color');
            $table->string('button_text_color', 7)->nullable()->after('button_background_color');
            $table->string('title_color', 7)->nullable()->after('button_text_color');
            $table->string('price_color', 7)->nullable()->after('title_color');
            $table->string('badge_color', 7)->nullable()->after('price_color');
            $table->string('feature_text_color', 7)->nullable()->after('badge_color');
            $table->boolean('show_on_website')->default(true)->after('status');
        });

        DB::table('plans')->orderBy('id')->each(function (object $plan): void {
            $updates = [];

            if (filled($plan->accent_color)) {
                $updates['badge_color'] = $plan->accent_color;
                $updates['card_border_color'] = $plan->accent_color;
            }

            if ($plan->is_featured) {
                $updates['show_badge'] = true;
            }

            $features = json_decode($plan->features ?? '[]', true) ?: [];
            $excluded = json_decode($plan->excluded_features ?? '[]', true) ?: [];

            if ($features !== [] && is_string($features[0] ?? null)) {
                $structured = [];
                $order = 1;

                foreach ($features as $title) {
                    $structured[] = [
                        'title' => $title,
                        'status' => 'active',
                        'icon_type' => 'check',
                        'sort_order' => $order++,
                    ];
                }

                foreach ($excluded as $title) {
                    $structured[] = [
                        'title' => $title,
                        'status' => 'disabled',
                        'icon_type' => 'cross',
                        'sort_order' => $order++,
                    ];
                }

                $updates['features'] = json_encode($structured);
            }

            if ($updates !== []) {
                DB::table('plans')->where('id', $plan->id)->update($updates);
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'type_label',
                'currency_symbol',
                'price_suffix',
                'show_badge',
                'show_payment_methods',
                'payment_methods',
                'card_border_color',
                'card_background_color',
                'button_background_color',
                'button_text_color',
                'title_color',
                'price_color',
                'badge_color',
                'feature_text_color',
                'show_on_website',
            ]);
        });
    }
};
