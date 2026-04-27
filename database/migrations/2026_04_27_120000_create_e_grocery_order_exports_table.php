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
        Schema::create('e_grocery_order_exports', function (Blueprint $table) {
            $table->id();
            $table->string('external_order_id')->unique();
            $table->string('source', 64)->default('familiaMogi-api')->index();
            $table->string('status', 32)->default('queued')->index();
            $table->json('request_payload');
            $table->json('normalized_payload');
            $table->json('panel_response')->nullable();
            $table->string('panel_order_id')->nullable()->index();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('exported_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_grocery_order_exports');
    }
};

