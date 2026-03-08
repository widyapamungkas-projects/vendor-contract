<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('hotel_contracts', function (Blueprint $table) {
            $table->string('destination')->nullable()->after('hotel_country');
            $table->string('area')->nullable()->after('destination');
        });
    }
    public function down(): void {
        Schema::table('hotel_contracts', function (Blueprint $table) {
            $table->dropColumn(['destination', 'area']);
        });
    }
};
