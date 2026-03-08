<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('restaurant_menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_contract_id')
                  ->constrained('restaurant_contracts')->onDelete('cascade');
            $table->string('menu_name');
            $table->enum('serving_style', ['set_menu', 'family_set', 'buffet'])->default('set_menu');
            $table->decimal('adult_price', 15, 2)->default(0);
            $table->decimal('child_price', 15, 2)->default(0);
            $table->integer('min_pax')->nullable();
            $table->longText('menu_details')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('restaurant_menus'); }
};
