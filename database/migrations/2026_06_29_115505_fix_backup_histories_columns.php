<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backup_histories', function (Blueprint $table) {
            $table->renameColumn('file_name', 'filename');
            $table->renameColumn('backup_type', 'type');
        });

        Schema::table('backup_histories', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('backup_histories', function (Blueprint $table) {
            $table->dropColumn(['status', 'started_at', 'completed_at', 'notes']);
        });

        Schema::table('backup_histories', function (Blueprint $table) {
            $table->renameColumn('filename', 'file_name');
            $table->renameColumn('type', 'backup_type');
        });
    }
};
