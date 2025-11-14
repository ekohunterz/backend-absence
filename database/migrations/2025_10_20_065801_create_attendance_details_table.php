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
        Schema::create('attendance_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('location_in', 255)->nullable();
            $table->string('location_out', 255)->nullable();
            $table->string('photo_in', 255)->nullable();
            $table->string('photo_out', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_details');
    }
};
