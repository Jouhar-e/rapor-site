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
        Schema::dropIfExists('competency_templates');
    }

    public function down(): void
    {
        Schema::create('competency_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('predicate');
            $table->text('achievement_text');
            $table->text('improvement_text')->nullable();
            $table->timestamps();
        });
    }
};
