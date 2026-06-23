<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cv_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_id')->constrained('cvs')->cascadeOnDelete();
            $table->string('type');
            $table->string('title')->nullable();
            $table->json('json_data')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cv_id', 'sort_order']);
            $table->index(['cv_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_sections');
    }
};
