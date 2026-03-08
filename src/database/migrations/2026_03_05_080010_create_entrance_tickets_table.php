<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('entrance_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('attraction_type', [
                'temple','museum','theme_park','natural_attraction',
                'cultural_show','zoo_safari','other'
            ])->default('other');
            $table->string('attraction_name');
            $table->string('currency', 10)->default('IDR');
            $table->decimal('adult_price', 15, 2)->default(0);
            $table->decimal('child_price', 15, 2)->default(0);
            $table->decimal('infant_price', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('entrance_tickets'); }
};
