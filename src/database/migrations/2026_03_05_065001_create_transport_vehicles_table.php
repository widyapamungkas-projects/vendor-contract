<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transport_contract_id')
                  ->constrained('transport_contracts')
                  ->onDelete('cascade');
            $table->enum('category', ['car', 'van', 'bus'])->default('car');
            $table->string('vehicle_name');
            $table->integer('capacity')->nullable();
            $table->string('brand')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_vehicles');
    }
};