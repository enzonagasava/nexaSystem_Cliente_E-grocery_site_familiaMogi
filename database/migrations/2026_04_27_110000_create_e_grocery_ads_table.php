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
        Schema::create('e_grocery_ads', function (Blueprint $table) {
            $table->id();
            $table->string('external_ad_id')->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 32)->nullable()->index();
            $table->integer('priority')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
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
        Schema::dropIfExists('e_grocery_ads');
    }
};

