<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('promotion_mappings')) {
            return;
        }

        Schema::table('promotion_mappings', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('promoted_at')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('promotion_mappings')) {
            return;
        }

        Schema::table('promotion_mappings', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['academic_year_id', 'promoted_at', 'notes']);
        });
    }
};
