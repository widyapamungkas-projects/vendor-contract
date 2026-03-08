<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('season_surcharges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('hotel_contract_id')->constrained('hotel_contracts')->cascadeOnDelete();
            $table->string('season_name');
            $table->decimal('surcharge_amount', 15, 2);
            $table->enum('surcharge_type', ['fixed', 'percentage'])->default('fixed');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_surcharges');
    }
};
