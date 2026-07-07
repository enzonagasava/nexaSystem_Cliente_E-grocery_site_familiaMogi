<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('e_grocery_products', function (Blueprint $table) {
            $table->id();
            $table->string('external_sku', 120)->unique();
            $table->string('name')->nullable();
            $table->string('category')->nullable()->index();
            $table->decimal('price', 12, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->string('status', 32)->nullable()->index();
            $table->string('external_image_id')->nullable()->index();
            $table->timestamp('source_updated_at')->nullable()->index();
            $table->json('payload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_grocery_products');
    }
};

