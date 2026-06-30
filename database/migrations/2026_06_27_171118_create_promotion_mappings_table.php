<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('destination_class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('promoted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_mappings');
    }
};
