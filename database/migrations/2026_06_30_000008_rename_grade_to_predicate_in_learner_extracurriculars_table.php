<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learner_extracurriculars', function (Blueprint $table) {
            $table->renameColumn('grade', 'predicate');
        });
    }

    public function down(): void
    {
        Schema::table('learner_extracurriculars', function (Blueprint $table) {
            $table->renameColumn('predicate', 'grade');
        });
    }
};
