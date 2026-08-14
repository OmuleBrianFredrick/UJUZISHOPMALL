<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('provider', 30);
            $table->string('method', 40);
            $table->string('phone', 30);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('UGX');
            $table->string('status', 30)->default('pending');
            $table->string('merchant_reference')->unique();
            $table->string('provider_reference')->nullable()->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason')->nullable();
            $table->json('provider_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['seller_id', 'status', 'created_at']);
        });
    }

    public function down(): void { Schema::dropIfExists('payouts'); }
};
