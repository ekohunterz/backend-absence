<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grade_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('old_grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->foreignId('new_grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->date('promotion_date');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('student_id');
            $table->index('promotion_date');
            $table->index('academic_year_id');
        });

        // Add order column to grades table if not exists
        if (!Schema::hasColumn('grades', 'order')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->integer('order')->default(0)->after('name');
            });
        }

        // Add capacity column to grades table if not exists
        if (!Schema::hasColumn('grades', 'capacity')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->integer('capacity')->nullable()->after('order');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_histories');

        if (Schema::hasColumn('grades', 'order')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }

        if (Schema::hasColumn('grades', 'capacity')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->dropColumn('capacity');
            });
        }
    }
};