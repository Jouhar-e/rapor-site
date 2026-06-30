<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('task_percentage', 5, 2);
            $table->decimal('pts_percentage', 5, 2);
            $table->decimal('pas_percentage', 5, 2);
            $table->decimal('practice_percentage', 5, 2);
            $table->decimal('min_score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->default(100);
            $table->integer('rounding_digits')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_settings');
    }
};
