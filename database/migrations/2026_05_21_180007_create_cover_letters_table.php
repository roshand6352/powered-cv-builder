<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cover_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cv_id')->nullable()->constrained('cvs')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->json('json_data')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'slug']);
            $table->index(['user_id', 'status']);
            $table->index('cv_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cover_letters');
    }
};
