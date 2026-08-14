<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('seller_profiles')) {
            Schema::create('seller_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->string('store_name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('location')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();
                $table->index('status');
            });
        }

        if (!Schema::hasColumn('products', 'seller_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('seller_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                $table->index('seller_id');
            });
        }

        if (!Schema::hasColumn('order_items', 'seller_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('seller_id')->nullable()->after('product_id')->constrained('users')->nullOnDelete();
                $table->index('seller_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('order_items', 'seller_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['seller_id']);
                $table->dropIndex(['seller_id']);
                $table->dropColumn('seller_id');
            });
        }

        if (Schema::hasColumn('products', 'seller_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['seller_id']);
                $table->dropIndex(['seller_id']);
                $table->dropColumn('seller_id');
            });
        }

        Schema::dropIfExists('seller_profiles');
    }
};
