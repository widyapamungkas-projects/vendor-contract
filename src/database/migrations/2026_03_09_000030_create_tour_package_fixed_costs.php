<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tour_package_fixed_costs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tour_package_id');
            $table->string('cost_type'); // transport, guide, mineral_water, flower_garland, guide_allowance, luggage_truck
            $table->string('label');
            $table->decimal('amount', 15, 2)->default(0);
            $table->json('meta')->nullable(); // store config: ref_id, route_type, vehicle_id, etc.
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('tour_package_id')->references('id')->on('tour_packages')->onDelete('cascade');
        });

        // Add mineral water and flower garland config to tour_packages
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->integer('mw_bottles_per_day')->default(2)->after('currency');
            $table->decimal('mw_price_per_dus', 15, 2)->default(30000)->after('mw_bottles_per_day');
            $table->decimal('fg_garland_price', 15, 2)->default(50000)->after('mw_price_per_dus');
            $table->decimal('fg_flower_girl_price', 15, 2)->default(150000)->after('fg_garland_price');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tour_package_fixed_costs');
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn(['mw_bottles_per_day','mw_price_per_dus','fg_garland_price','fg_flower_girl_price']);
        });
    }
};
