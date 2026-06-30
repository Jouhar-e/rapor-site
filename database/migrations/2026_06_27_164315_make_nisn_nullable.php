<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE learners MODIFY nisn VARCHAR(255) NULL');
        DB::statement('ALTER TABLE learners DROP INDEX learners_nisn_unique');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE learners MODIFY nisn VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE learners ADD UNIQUE INDEX learners_nisn_unique (nisn)');
    }
};
