<?php

use App\Models\Grade;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->enum('gender', ['L', 'P']);
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('password', 255);
            $table->string('avatar_url', 255)->nullable();
            $table->foreignIdFor(Grade::class)->constrained()->cascadeOnDelete();
            $table->enum('status', ['aktif', 'non-aktif', 'keluar', 'lulus'])->default('aktif');
            $table->string('remember_token', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
