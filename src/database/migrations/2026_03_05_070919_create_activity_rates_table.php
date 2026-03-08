<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('activity_item_id')
                  ->constrained('activity_items')
                  ->onDelete('cascade');
            $table->enum('rate_type', ['per_pax', 'per_group'])->default('per_pax');
            $table->enum('pax_type', ['adult', 'child', 'infant'])->nullable();
            $table->integer('min_pax')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_rates');
    }
};