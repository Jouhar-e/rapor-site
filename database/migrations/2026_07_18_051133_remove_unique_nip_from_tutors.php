<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropUnique('tutors_nip_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->string('nip')->unique();
        });
    }
};
