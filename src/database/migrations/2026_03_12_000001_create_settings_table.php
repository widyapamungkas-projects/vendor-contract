<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        $now = now();
        DB::table('settings')->insert([
            ['key' => 'company_name',    'value' => 'Diorama Destination',        'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_tagline', 'value' => 'Tour & Travel Specialist',   'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_address', 'value' => '',                           'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_phone',   'value' => '',                           'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_email',   'value' => '',                           'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_website', 'value' => '',                           'created_at' => $now, 'updated_at' => $now],
            ['key' => 'brand_logo',      'value' => null,                         'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_logo',    'value' => null,                         'created_at' => $now, 'updated_at' => $now],
            ['key' => 'proposal_footer_text', 'value' => 'Confidential — For recipient use only', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('settings');
    }
};