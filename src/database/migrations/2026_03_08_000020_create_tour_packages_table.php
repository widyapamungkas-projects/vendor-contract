<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('package_code')->unique();
            $table->string('agent');
            $table->string('destination')->nullable();
            $table->string('duration')->nullable();
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->integer('pax')->default(1);
            $table->string('currency', 10)->default('IDR');
            $table->decimal('rate_usd', 15, 2)->default(14000);
            $table->decimal('rate_myr', 15, 2)->default(3500);
            $table->decimal('rate_sgd', 15, 2)->default(10500);
            $table->decimal('rate_eur', 15, 2)->default(15500);
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tour_package_la_costs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tour_package_id');
            $table->string('name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->foreign('tour_package_id')->references('id')->on('tour_packages')->onDelete('cascade');
        });

        Schema::create('tour_package_itinerary', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tour_package_id');
            $table->integer('day');
            $table->string('item_name');
            $table->enum('item_type', ['entrance','activity','restaurant','manual']);
            $table->uuid('ref_id')->nullable();
            $table->decimal('price_per_pax', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->foreign('tour_package_id')->references('id')->on('tour_packages')->onDelete('cascade');
        });

        Schema::create('tour_package_hotels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tour_package_id');
            $table->uuid('hotel_contract_id')->nullable();
            $table->string('hotel_name');
            $table->string('room_type')->nullable();
            $table->decimal('room_rate', 15, 2)->default(0);
            $table->integer('nights')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->foreign('tour_package_id')->references('id')->on('tour_packages')->onDelete('cascade');
        });

        Schema::create('tour_package_room_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tour_package_id')->unique();
            $table->integer('sgl')->default(0);
            $table->integer('twn')->default(0);
            $table->integer('trp')->default(0);
            $table->integer('foc')->default(0);
            $table->decimal('margin_twn', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('tour_package_id')->references('id')->on('tour_packages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_room_configs');
        Schema::dropIfExists('tour_package_hotels');
        Schema::dropIfExists('tour_package_itinerary');
        Schema::dropIfExists('tour_package_la_costs');
        Schema::dropIfExists('tour_packages');
    }
};
