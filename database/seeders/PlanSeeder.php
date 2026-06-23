<?php

namespace Database\Seeders;

use App\Enums\PlanSlug;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => PlanSlug::Free->value,
                'type_label' => 'FREE',
                'description' => 'Everything you need to get started.',
                'price' => 0,
                'currency' => 'ZAR',
                'currency_symbol' => 'R',
                'price_suffix' => '/ forever free',
                'ai_credits_limit' => 10,
                'billing_interval' => 'free',
                'duration_hours' => null,
                'duration_days' => null,
                'is_lifetime' => false,
                'has_watermark' => true,
                'is_featured' => false,
                'show_badge' => false,
                'sort_order' => 1,
                'status' => 'active',
                'show_on_website' => true,
                'button_text' => 'Get started free',
                'button_link' => 'register',
                'button_style' => 'secondary',
                'footer_note' => 'Free downloads include an ApplyDocs ZA watermark',
                'show_payment_methods' => false,
                'features' => [
                    ['title' => 'CV Builder with guided prompts', 'status' => 'active', 'icon_type' => 'check', 'sort_order' => 1],
                    ['title' => 'Cover Letter Generator', 'status' => 'active', 'icon_type' => 'check', 'sort_order' => 2],
                    ['title' => '3 ATS-optimised templates', 'status' => 'active', 'icon_type' => 'check', 'sort_order' => 3],
                    ['title' => 'PDF download', 'status' => 'active', 'icon_type' => 'check', 'sort_order' => 4],
                    ['title' => 'Edit and save your CV anytime', 'status' => 'active', 'icon_type' => 'check', 'sort_order' => 5],
                    ['title' => 'AI writing assistance (Pro feature)', 'status' => 'disabled', 'icon_type' => 'cross', 'sort_order' => 6],
                    ['title' => 'Watermark-free PDF (Pro feature)', 'status' => 'disabled', 'icon_type' => 'cross', 'sort_order' => 7],
                ],
            ],
            [
                'name' => 'Pro',
                'slug' => PlanSlug::Pro->value,
                'type_label' => 'PRO',
                'description' => 'Full premium access for 30 days. Pay once, no subscription.',
                'price' => 80,
                'currency' => 'ZAR',
                'currency_symbol' => 'R',
                'price_suffix' => '/ 30 days',
                'ai_credits_limit' => 500,
                'billing_interval' => 'once',
                'duration_hours' => null,
                'duration_days' => 30,
                'is_lifetime' => false,
                'has_watermark' => false,
                'is_featured' => true,
                'show_badge' => true,
                'badge_text' => 'MOST POPULAR',
                'badge_color' => '#C1623D',
                'card_border_color' => '#C1623D',
                'button_style' => 'primary',
                'sort_order' => 2,
                'status' => 'active',
                'show_on_website' => true,
                'button_text' => 'Upgrade to Pro',
                'button_link' => 'pricing',
                'show_payment_methods' => true,
                'payment_methods' => [
                    ['label' => 'Card'],
                    ['label' => 'EFT'],
                    ['label' => 'Capitec Pay'],
                    ['label' => 'and more'],
                ],
                'features' => [
                    ['title' => 'Everything in Free, plus:', 'status' => 'active', 'icon_type' => 'check', 'sort_order' => 1],
                    ['title' => 'AI writing assistance', 'status' => 'active', 'icon_type' => 'star', 'sort_order' => 2],
                    ['title' => 'Watermark-free PDF export', 'status' => 'active', 'icon_type' => 'star', 'sort_order' => 3],
                    ['title' => 'All premium templates', 'status' => 'active', 'icon_type' => 'star', 'sort_order' => 4],
                    ['title' => 'Cover letter AI', 'status' => 'active', 'icon_type' => 'star', 'sort_order' => 5],
                    ['title' => 'Priority PDF export', 'status' => 'active', 'icon_type' => 'star', 'sort_order' => 6],
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::query()->updateOrCreate(
                ['slug' => $plan['slug']],
                $plan,
            );
        }

        $freePlan = Plan::query()->where('slug', PlanSlug::Free->value)->first();

        if ($freePlan) {
            User::query()->whereNull('plan_id')->update(['plan_id' => $freePlan->id]);
        }
    }
}
