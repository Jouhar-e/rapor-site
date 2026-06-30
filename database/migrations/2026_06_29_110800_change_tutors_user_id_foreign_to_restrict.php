<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tutors', function ($table) {
            $table->dropForeign('tutors_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tutors', function ($table) {
            $table->dropForeign('tutors_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
