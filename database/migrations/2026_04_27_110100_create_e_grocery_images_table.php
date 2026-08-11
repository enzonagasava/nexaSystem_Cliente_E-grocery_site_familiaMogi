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
        Schema::create('e_grocery_images', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->string('external_image_id')->unique();
            $table->string('storage_key')->nullable();
            $table->text('url')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('checksum', 191)->nullable();
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
        Schema::dropIfExists('e_grocery_images');
    }
};

