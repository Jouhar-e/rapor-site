<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homeroom_teachers', function (Blueprint $table) {
            $table->dropUnique('homeroom_teachers_tutor_id_class_id_academic_year_id_unique');
            $table->dropColumn('tutor_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
            $table->unique(['user_id', 'class_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::table('homeroom_teachers', function (Blueprint $table) {
            $table->dropUnique('homeroom_teachers_user_id_class_id_academic_year_id_unique');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignId('tutor_id')->constrained('tutors')->cascadeOnDelete();
            $table->unique(['tutor_id', 'class_id', 'academic_year_id']);
        });
    }
};
