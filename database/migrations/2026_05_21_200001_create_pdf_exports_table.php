<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('exportable');
            $table->string('type');
            $table->string('filename');
            $table->string('disk')->default('local');
            $table->string('path')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->json('options')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_exports');
    }
};
