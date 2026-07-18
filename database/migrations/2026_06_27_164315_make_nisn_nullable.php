<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('learners')) {
            return;
        }

        DB::statement('ALTER TABLE learners MODIFY nisn VARCHAR(255) NULL');
        DB::statement('ALTER TABLE learners DROP INDEX learners_nisn_unique');
    }

    public function down(): void
    {
        if (! Schema::hasTable('learners')) {
            return;
        }

        DB::statement('ALTER TABLE learners MODIFY nisn VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE learners ADD UNIQUE INDEX learners_nisn_unique (nisn)');
    }
};
