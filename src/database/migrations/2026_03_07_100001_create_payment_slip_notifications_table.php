<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payment_slip_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('file_id');         // Google Drive file ID
            $table->string('file_name');
            $table->string('folder_id');
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('payment_slip_notifications');
    }
};
