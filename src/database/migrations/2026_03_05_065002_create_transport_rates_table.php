<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transport_vehicle_id')
                  ->constrained('transport_vehicles')
                  ->onDelete('cascade');
            $table->string('route_name');
            $table->enum('route_type', [
                'airport_transfer',
                'half_day',
                'full_day',
                'half_day_dinner',
                'full_day_dinner',
                'overnight',
                'extra_hour',
            ])->default('airport_transfer');
            $table->string('duration')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_rates');
    }
};