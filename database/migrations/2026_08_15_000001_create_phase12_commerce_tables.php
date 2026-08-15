<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
            });
        }

        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('body');
                $table->string('status')->default('pending');
                $table->boolean('verified_purchase')->default(false);
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
                $table->index(['product_id', 'status']);
            });
        }

        if (!Schema::hasTable('promotions')) {
            Schema::create('promotions', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('type', 20);
                $table->decimal('value', 12, 2);
                $table->decimal('minimum_order', 12, 2)->default(0);
                $table->unsignedInteger('usage_limit')->nullable();
                $table->unsignedInteger('usage_count')->default(0);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
                $table->index(['active', 'starts_at', 'ends_at']);
            });
        }

        if (!Schema::hasTable('loyalty_transactions')) {
            Schema::create('loyalty_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
                $table->string('type', 30);
                $table->integer('points');
                $table->string('reference')->nullable()->index();
                $table->string('description')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('wishlists');
    }
};
