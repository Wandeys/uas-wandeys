<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->decimal('score_attendance', 5, 2)->default(0);
            $table->decimal('score_task', 5, 2)->default(0);
            $table->decimal('score_uts', 5, 2)->default(0);
            $table->decimal('score_uas', 5, 2)->default(0);
            $table->decimal('score_final', 5, 2)->default(0);
            $table->string('grade_letter', 2)->nullable();
            $table->decimal('quality_point', 3, 2)->default(0);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
