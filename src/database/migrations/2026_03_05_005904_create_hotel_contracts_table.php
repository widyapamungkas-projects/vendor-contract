<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('contract_code')->unique();
            $table->string('hotel_name');
            $table->string('hotel_address')->nullable();
            $table->string('hotel_city');
            $table->string('hotel_country')->default('Indonesia');
            $table->string('pic_name');
            $table->string('pic_phone')->nullable();
            $table->string('pic_email')->nullable();
            $table->enum('contract_type', ['regular', 'campaign']);
            $table->enum('price_category', ['FIT', 'GIT']);
            $table->string('currency', 10)->default('IDR');
            $table->date('valid_from');
            $table->date('valid_until');
            $table->enum('status', ['active', 'expired', 'expiring_soon'])->default('active');
            $table->text('notes')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_contracts');
    }
};