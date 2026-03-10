<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tour_package_room_configs', function (Blueprint $table) {
            $table->integer('nights')->default(0)->after('margin_twn');
            $table->decimal('margin_room', 12, 2)->default(25000)->after('nights');
            $table->decimal('hd_meeting', 12, 2)->default(0)->after('margin_room');
            $table->decimal('fd_meeting', 12, 2)->default(0)->after('hd_meeting');
            $table->decimal('dinner_cost', 12, 2)->default(0)->after('fd_meeting');
            $table->tinyInteger('with_tl')->default(0)->after('dinner_cost');
        });
    }

    public function down(): void
    {
        Schema::table('tour_package_room_configs', function (Blueprint $table) {
            $table->dropColumn(['nights','margin_room','hd_meeting','fd_meeting','dinner_cost','with_tl']);
        });
    }
};