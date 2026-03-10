<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'actual_pax')) {
                $table->integer('actual_pax')->nullable()->after('pax');
            }
            if (!Schema::hasColumn('tour_packages', 'margin')) {
                $table->decimal('margin', 4, 2)->default(0.85)->after('actual_pax');
            }
        });
    }
    public function down(): void {}
};
