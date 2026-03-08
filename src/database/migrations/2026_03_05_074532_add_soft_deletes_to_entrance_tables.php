<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrance_contracts', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('entrance_tickets', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('entrance_rates', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('entrance_contracts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('entrance_tickets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('entrance_rates', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};