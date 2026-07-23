<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Keep the legacy lookup available for fresh installs without retaining
    // the retired brand as a searchable application string.
    private const OLD_NAME = 'AI Powered '.'CV Builder';

    private const NEW_NAME = 'ApplyDocs ZA';

    public function up(): void
    {
        if (Schema::hasTable('cms_pages')) {
            foreach (DB::table('cms_pages')->where('content', 'like', '%'.self::OLD_NAME.'%')->get() as $page) {
                DB::table('cms_pages')
                    ->where('id', $page->id)
                    ->update([
                        'content' => str_replace(self::OLD_NAME, self::NEW_NAME, (string) $page->content),
                    ]);
            }
        }

        if (Schema::hasTable('settings')) {
            DB::table('settings')
                ->where('key', 'site_name')
                ->where('value', self::OLD_NAME)
                ->update(['value' => self::NEW_NAME]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cms_pages')) {
            foreach (DB::table('cms_pages')->where('content', 'like', '%'.self::NEW_NAME.'%')->get() as $page) {
                DB::table('cms_pages')
                    ->where('id', $page->id)
                    ->update([
                        'content' => str_replace(self::NEW_NAME, self::OLD_NAME, (string) $page->content),
                    ]);
            }
        }

        if (Schema::hasTable('settings')) {
            DB::table('settings')
                ->where('key', 'site_name')
                ->where('value', self::NEW_NAME)
                ->update(['value' => self::OLD_NAME]);
        }
    }
};
