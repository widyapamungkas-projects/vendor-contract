<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Guide language categories (Bahasa Guide)
        Schema::create('guide_languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('language_name'); // English, Mandarin, Japanese, dll
            $table->string('currency', 10)->default('IDR');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Service types per language (Full Day, Half Day, Airport Transfer, dll)
        Schema::create('guide_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('guide_language_id');
            $table->string('service_name'); // Full Day, Half Day, Airport Transfer, dll
            $table->enum('service_type', ['airport_transfer','full_day','half_day','overtime','tipping','package']);
            $table->string('unit_label')->default('per trip'); // per trip, per hari, per jam, dll
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('guide_language_id')->references('id')->on('guide_languages')->onDelete('cascade');
        });

        // Pax tiers per service
        Schema::create('guide_service_tiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('guide_service_id');
            $table->integer('min_pax');
            $table->integer('max_pax')->nullable(); // null = no limit
            $table->decimal('rate', 15, 2);
            $table->timestamps();
            $table->foreign('guide_service_id')->references('id')->on('guide_services')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_service_tiers');
        Schema::dropIfExists('guide_services');
        Schema::dropIfExists('guide_languages');
    }
};
