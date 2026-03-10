<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->longText('prop_inclusion')->nullable();
            $table->longText('prop_exclusion')->nullable();
            $table->longText('prop_tnc')->nullable();
            $table->longText('prop_itinerary')->nullable();
            $table->longText('prop_menu')->nullable();
            $table->json('prop_custom_tables')->nullable();
            $table->json('prop_itin_briefs')->nullable();
        });
    }
    public function down(): void {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn(['prop_inclusion','prop_exclusion','prop_tnc','prop_itinerary','prop_menu','prop_custom_tables','prop_itin_briefs']);
        });
    }
};