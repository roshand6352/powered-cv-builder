<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'profile_image')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('profile_image')->nullable()->after('email');
            });
        }

        if (! Schema::hasColumn('users', 'profile_photo')) {
            return;
        }

        DB::table('users')
            ->whereNotNull('profile_photo')
            ->orderBy('id')
            ->each(function (object $user): void {
                $legacyPath = (string) $user->profile_photo;

                if ($legacyPath === '') {
                    return;
                }

                $relativePath = str_starts_with($legacyPath, 'profile-images/')
                    ? $legacyPath
                    : 'profile-images/'.$legacyPath;

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['profile_image' => $relativePath]);
            });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('profile_photo');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'profile_photo')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('profile_photo')->nullable()->after('email');
            });
        }

        if (! Schema::hasColumn('users', 'profile_image')) {
            return;
        }

        DB::table('users')
            ->whereNotNull('profile_image')
            ->orderBy('id')
            ->each(function (object $user): void {
                $relativePath = (string) $user->profile_image;
                $filename = str_starts_with($relativePath, 'profile-images/')
                    ? substr($relativePath, strlen('profile-images/'))
                    : $relativePath;

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['profile_photo' => $filename]);
            });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('profile_image');
        });
    }
};
