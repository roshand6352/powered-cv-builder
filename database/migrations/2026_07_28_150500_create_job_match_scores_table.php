<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_match_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cv_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cover_letter_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('job_description');
            $table->integer('score')->nullable();
            $table->longText('result_json')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_match_scores');
    }
};
