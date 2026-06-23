<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cover_letters', function (Blueprint $table) {
            $table->foreignId('template_id')
                ->nullable()
                ->after('cv_id')
                ->constrained('cv_templates')
                ->nullOnDelete();

            $table->string('company_name')->nullable()->after('title');
            $table->string('job_title')->nullable()->after('company_name');
            $table->string('hiring_manager_name')->nullable()->after('job_title');
            $table->text('company_address')->nullable()->after('hiring_manager_name');
            $table->string('subject')->nullable()->after('company_address');
            $table->string('greeting')->nullable()->after('subject');
            $table->text('introduction')->nullable()->after('greeting');
            $table->longText('body')->nullable()->after('introduction');
            $table->text('closing_paragraph')->nullable()->after('body');
            $table->string('signature_name')->nullable()->after('closing_paragraph');
        });
    }

    public function down(): void
    {
        Schema::table('cover_letters', function (Blueprint $table) {
            $table->dropConstrainedForeignId('template_id');
            $table->dropColumn([
                'company_name',
                'job_title',
                'hiring_manager_name',
                'company_address',
                'subject',
                'greeting',
                'introduction',
                'body',
                'closing_paragraph',
                'signature_name',
            ]);
        });
    }
};
