<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cv_templates') || ! Schema::hasColumn('cv_templates', 'preview_image')) {
            return;
        }

        $disk = Storage::disk('public');

        DB::table('cv_templates')
            ->whereNotNull('preview_image')
            ->where('preview_image', '!=', '')
            ->orderBy('id')
            ->pluck('preview_image')
            ->unique()
            ->each(function (string $path) use ($disk): void {
                if ($disk->exists($path)) {
                    $disk->delete($path);
                }
            });

        $previewsDirectory = 'cv-templates/previews';

        if ($disk->exists($previewsDirectory)) {
            $disk->deleteDirectory($previewsDirectory);
        }

        Schema::table('cv_templates', function (Blueprint $table): void {
            $table->dropColumn('preview_image');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cv_templates') || Schema::hasColumn('cv_templates', 'preview_image')) {
            return;
        }

        Schema::table('cv_templates', function (Blueprint $table): void {
            $table->string('preview_image')->nullable()->after('description');
        });
    }
};
