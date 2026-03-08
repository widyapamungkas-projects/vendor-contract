<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('hotel_contract_id')->constrained('hotel_contracts')->cascadeOnDelete();
            $table->string('room_type');
            $table->decimal('rate', 15, 2);
            $table->decimal('extra_bed_rate', 15, 2)->default(0);
            $table->decimal('breakfast_rate', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_rates');
    }
};
