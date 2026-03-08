<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_contracts', function (Blueprint $table) {
            $table->foreignUuid('created_by')->nullable()->change();
            $table->foreignUuid('updated_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('hotel_contracts', function (Blueprint $table) {
            $table->foreignUuid('created_by')->nullable(false)->change();
            $table->foreignUuid('updated_by')->nullable(false)->change();
        });
    }
};