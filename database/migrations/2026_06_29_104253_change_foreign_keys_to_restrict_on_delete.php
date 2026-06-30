<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Programs → children: CASCADE → RESTRICT
        Schema::table('classes', function ($table) {
            $table->dropForeign('classes_program_id_foreign');
            $table->foreign('program_id')->references('id')->on('programs')->restrictOnDelete();
        });

        Schema::table('learners', function ($table) {
            $table->dropForeign('learners_program_id_foreign');
            $table->foreign('program_id')->references('id')->on('programs')->restrictOnDelete();
        });

        Schema::table('subjects', function ($table) {
            $table->dropForeign('subjects_program_id_foreign');
            $table->foreign('program_id')->references('id')->on('programs')->restrictOnDelete();
        });

        // Learners → children: CASCADE → RESTRICT
        Schema::table('grades', function ($table) {
            $table->dropForeign('grades_learner_id_foreign');
            $table->foreign('learner_id')->references('id')->on('learners')->restrictOnDelete();
        });

        Schema::table('attendances', function ($table) {
            $table->dropForeign('attendances_learner_id_foreign');
            $table->foreign('learner_id')->references('id')->on('learners')->restrictOnDelete();
        });

        Schema::table('class_learners', function ($table) {
            $table->dropForeign('class_learners_learner_id_foreign');
            $table->foreign('learner_id')->references('id')->on('learners')->restrictOnDelete();
        });

        Schema::table('homeroom_notes', function ($table) {
            $table->dropForeign('homeroom_notes_learner_id_foreign');
            $table->foreign('learner_id')->references('id')->on('learners')->restrictOnDelete();
        });

        Schema::table('learner_extracurriculars', function ($table) {
            $table->dropForeign('learner_extracurriculars_learner_id_foreign');
            $table->foreign('learner_id')->references('id')->on('learners')->restrictOnDelete();
        });

        // Classes → children: CASCADE → RESTRICT
        Schema::table('subjects', function ($table) {
            $table->dropForeign('subjects_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->restrictOnDelete();
        });

        Schema::table('class_learners', function ($table) {
            $table->dropForeign('class_learners_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->restrictOnDelete();
        });

        Schema::table('homeroom_teachers', function ($table) {
            $table->dropForeign('homeroom_teachers_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->restrictOnDelete();
        });

        Schema::table('promotion_mappings', function ($table) {
            $table->dropForeign('promotion_mappings_source_class_id_foreign');
            $table->foreign('source_class_id')->references('id')->on('classes')->restrictOnDelete();
        });

        Schema::table('promotion_mappings', function ($table) {
            $table->dropForeign('promotion_mappings_destination_class_id_foreign');
            $table->foreign('destination_class_id')->references('id')->on('classes')->restrictOnDelete();
        });

        // Subjects → children: CASCADE → RESTRICT
        Schema::table('grades', function ($table) {
            $table->dropForeign('grades_subject_id_foreign');
            $table->foreign('subject_id')->references('id')->on('subjects')->restrictOnDelete();
        });

        // Extracurriculars → children: CASCADE → RESTRICT
        Schema::table('learner_extracurriculars', function ($table) {
            $table->dropForeign('learner_extracurriculars_extracurricular_id_foreign');
            $table->foreign('extracurricular_id')->references('id')->on('extracurriculars')->restrictOnDelete();
        });

        // Academic years → children: CASCADE → RESTRICT
        Schema::table('semesters', function ($table) {
            $table->dropForeign('semesters_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->restrictOnDelete();
        });

        Schema::table('grades', function ($table) {
            $table->dropForeign('grades_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->restrictOnDelete();
        });

        Schema::table('attendances', function ($table) {
            $table->dropForeign('attendances_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->restrictOnDelete();
        });

        Schema::table('class_learners', function ($table) {
            $table->dropForeign('class_learners_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->restrictOnDelete();
        });

        Schema::table('homeroom_notes', function ($table) {
            $table->dropForeign('homeroom_notes_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->restrictOnDelete();
        });

        Schema::table('learner_extracurriculars', function ($table) {
            $table->dropForeign('learner_extracurriculars_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->restrictOnDelete();
        });

        // Semesters → children: CASCADE → RESTRICT
        Schema::table('grades', function ($table) {
            $table->dropForeign('grades_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->restrictOnDelete();
        });

        Schema::table('attendances', function ($table) {
            $table->dropForeign('attendances_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->restrictOnDelete();
        });

        Schema::table('learner_extracurriculars', function ($table) {
            $table->dropForeign('learner_extracurriculars_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->restrictOnDelete();
        });

        Schema::table('homeroom_notes', function ($table) {
            $table->dropForeign('homeroom_notes_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        // Classes → program: revert RESTRICT → CASCADE
        Schema::table('classes', function ($table) {
            $table->dropForeign('classes_program_id_foreign');
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
        });

        Schema::table('learners', function ($table) {
            $table->dropForeign('learners_program_id_foreign');
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
        });

        Schema::table('subjects', function ($table) {
            $table->dropForeign('subjects_program_id_foreign');
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->dropForeign('subjects_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        Schema::table('grades', function ($table) {
            $table->dropForeign('grades_learner_id_foreign');
            $table->foreign('learner_id')->references('id')->on('learners')->cascadeOnDelete();
            $table->dropForeign('grades_subject_id_foreign');
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->dropForeign('grades_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->dropForeign('grades_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->cascadeOnDelete();
        });

        Schema::table('attendances', function ($table) {
            $table->dropForeign('attendances_learner_id_foreign');
            $table->foreign('learner_id')->references('id')->on('learners')->cascadeOnDelete();
            $table->dropForeign('attendances_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->dropForeign('attendances_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->cascadeOnDelete();
        });

        Schema::table('class_learners', function ($table) {
            $table->dropForeign('class_learners_learner_id_foreign');
            $table->foreign('learner_id')->references('id')->on('learners')->cascadeOnDelete();
            $table->dropForeign('class_learners_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
            $table->dropForeign('class_learners_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
        });

        Schema::table('homeroom_notes', function ($table) {
            $table->dropForeign('homeroom_notes_learner_id_foreign');
            $table->foreign('learner_id')->references('id')->on('learners')->cascadeOnDelete();
            $table->dropForeign('homeroom_notes_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->dropForeign('homeroom_notes_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->cascadeOnDelete();
        });

        Schema::table('learner_extracurriculars', function ($table) {
            $table->dropForeign('learner_extracurriculars_learner_id_foreign');
            $table->foreign('learner_id')->references('id')->on('learners')->cascadeOnDelete();
            $table->dropForeign('learner_extracurriculars_extracurricular_id_foreign');
            $table->foreign('extracurricular_id')->references('id')->on('extracurriculars')->cascadeOnDelete();
            $table->dropForeign('learner_extracurriculars_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->dropForeign('learner_extracurriculars_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->cascadeOnDelete();
        });

        Schema::table('homeroom_teachers', function ($table) {
            $table->dropForeign('homeroom_teachers_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        Schema::table('promotion_mappings', function ($table) {
            $table->dropForeign('promotion_mappings_source_class_id_foreign');
            $table->foreign('source_class_id')->references('id')->on('classes')->cascadeOnDelete();
            $table->dropForeign('promotion_mappings_destination_class_id_foreign');
            $table->foreign('destination_class_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        Schema::table('semesters', function ($table) {
            $table->dropForeign('semesters_academic_year_id_foreign');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
        });
    }
};
