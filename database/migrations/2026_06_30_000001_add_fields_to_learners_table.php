<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learners', function (Blueprint $table) {
            $table->string('religion')->nullable()->after('gender');
            $table->unsignedTinyInteger('child_order')->nullable()->after('religion');
            $table->string('phone')->nullable()->after('child_order');
            $table->date('admission_date')->nullable()->after('phone');
            $table->string('admission_class')->nullable()->after('admission_date');
            $table->string('admission_status')->nullable()->after('admission_class');
            $table->string('father_name')->nullable()->after('admission_status');
            $table->string('father_job')->nullable()->after('father_name');
            $table->string('mother_name')->nullable()->after('father_job');
            $table->string('mother_job')->nullable()->after('mother_name');
            $table->string('guardian_name')->nullable()->after('mother_job');
            $table->string('guardian_job')->nullable()->after('guardian_name');
            $table->string('report_number')->nullable()->after('guardian_job');
        });
    }

    public function down(): void
    {
        Schema::table('learners', function (Blueprint $table) {
            $table->dropColumn([
                'religion', 'child_order', 'phone', 'admission_date',
                'admission_class', 'admission_status', 'father_name',
                'father_job', 'mother_name', 'mother_job', 'guardian_name',
                'guardian_job', 'report_number',
            ]);
        });
    }
};
