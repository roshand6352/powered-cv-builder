<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class CmsAndSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Home Hero',
                'slug' => 'home-hero',
                'content' => '<p>Welcome to ApplyDocs ZA — create professional resumes in minutes.</p>',
                'status' => 'published',
                'published_at' => now(),
                'meta' => ['section' => 'marketing'],
            ],
            [
                'title' => 'About Intro',
                'slug' => 'about-intro',
                'content' => '<p>We help job seekers build compelling CVs with AI assistance.</p>',
                'status' => 'published',
                'published_at' => now(),
                'meta' => ['section' => 'marketing'],
            ],
        ];

        foreach ($pages as $page) {
            CmsPage::query()->updateOrCreate(['slug' => $page['slug']], $page);
        }

        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => config('app.name'), 'type' => 'string', 'description' => 'Public site name'],
            ['group' => 'general', 'key' => 'support_email', 'value' => 'support@example.com', 'type' => 'string', 'description' => 'Support contact email'],
            ['group' => 'billing', 'key' => 'payfast_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable PayFast checkout'],
            ['group' => 'ai', 'key' => 'ai_default_credits', 'value' => '10', 'type' => 'integer', 'description' => 'Default free AI credits'],
            ['group' => 'ai', 'key' => 'openai_model', 'value' => 'gpt-4o-mini', 'type' => 'string', 'description' => 'Default OpenAI model'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
