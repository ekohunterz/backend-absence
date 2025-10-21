<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendance_details', function (Blueprint $table) {
            $table->foreignId('leave_request_id')->nullable()->constrained('leave_requests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('leave_request_id');
        });
    }
};
