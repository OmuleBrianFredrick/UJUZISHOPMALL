<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('password')->index();
        });

        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('store_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->after('id')->constrained('users')->nullOnDelete()->index();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->after('product_id')->constrained('users')->nullOnDelete()->index();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropColumn('seller_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropColumn('seller_id');
        });
        Schema::dropIfExists('seller_profiles');
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
