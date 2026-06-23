<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cvs', 'slug')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->dropForeign(['template_id']);
                $table->dropUnique(['user_id', 'slug']);
                $table->dropColumn(['slug', 'deleted_at']);
            });
        }

        if (Schema::hasColumn('cvs', 'json_data')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->renameColumn('json_data', 'cv_data');
            });
        }

        if (Schema::getColumnType('cvs', 'status') !== 'tinyint') {
            DB::table('cvs')->where('status', 'draft')->update(['status' => '1']);
            DB::table('cvs')->where('status', 'published')->update(['status' => '2']);

            Schema::table('cvs', function (Blueprint $table) {
                $table->unsignedTinyInteger('status')->default(1)->change();
            });
        }

        if (! $this->hasForeignKey('cvs', 'cvs_template_id_foreign')) {
            DB::table('cvs')->update(['template_id' => null]);

            Schema::table('cvs', function (Blueprint $table) {
                $table->foreign('template_id')
                    ->references('id')
                    ->on('cv_templates')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if ($this->hasForeignKey('cvs', 'cvs_template_id_foreign')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->dropForeign(['template_id']);
            });
        }

        if (Schema::getColumnType('cvs', 'status') === 'tinyint') {
            Schema::table('cvs', function (Blueprint $table) {
                $table->string('status')->default('draft')->change();
            });

            DB::table('cvs')->where('status', '1')->update(['status' => 'draft']);
            DB::table('cvs')->where('status', '2')->update(['status' => 'published']);
        }

        if (Schema::hasColumn('cvs', 'cv_data')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->renameColumn('cv_data', 'json_data');
            });
        }

        if (! Schema::hasColumn('cvs', 'slug')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->string('slug')->after('title');
                $table->softDeletes();
                $table->unique(['user_id', 'slug']);
                $table->foreign('template_id')
                    ->references('id')
                    ->on('templates')
                    ->nullOnDelete();
            });
        }
    }

    private function hasForeignKey(string $table, string $foreignKey): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = $connection->select(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$database, $table, $foreignKey, 'FOREIGN KEY']
        );

        return count($result) > 0;
    }
};
