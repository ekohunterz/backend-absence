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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('school_logo')->nullable();
            $table->string('school_phone')->nullable();
            $table->string('school_email')->nullable();
            $table->string('school_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();   // ex: -6.2000000
            $table->decimal('longitude', 10, 7)->nullable();  // ex: 106.8166667
            $table->integer('radius')->default(50); // in meters
            $table->time('start_time')->default('07:00:00'); // jam masuk
            $table->time('end_time')->default('15:00:00');   // jam pulang
            $table->time('check_in_tolerance')->default('08:00:00'); // jam masuk
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
