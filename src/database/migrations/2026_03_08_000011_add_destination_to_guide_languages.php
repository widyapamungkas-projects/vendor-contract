<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guide_languages', function (Blueprint $table) {
            $table->string('destination')->nullable()->after('language_name');
        });
    }
    public function down(): void
    {
        Schema::table('guide_languages', function (Blueprint $table) {
            $table->dropColumn('destination');
        });
    }
};
