<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('feature');
            $table->string('model')->nullable();
            $table->unsignedInteger('tokens_used')->default(0);
            $table->unsignedInteger('credits_used')->default(1);
            $table->json('request')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index('feature');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
