<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gateway');
            $table->string('transaction_id');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['gateway', 'transaction_id']);
            $table->index(['user_id', 'status']);
            $table->index('subscription_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
