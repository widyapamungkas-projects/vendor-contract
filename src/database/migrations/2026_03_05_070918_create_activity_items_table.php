<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('activity_contract_id')
                  ->constrained('activity_contracts')
                  ->onDelete('cascade');
            $table->enum('activity_type', [
                'rafting', 'cycling', 'trekking', 'water_sports',
                'cultural_tour', 'cooking_class', 'atv_offroad', 'spa_wellness', 'other'
            ])->default('other');
            $table->string('activity_name');
            $table->string('duration')->nullable();
            $table->integer('min_pax')->default(1);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_items');
    }
};