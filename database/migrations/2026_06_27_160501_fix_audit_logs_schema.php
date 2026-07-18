<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['user', 'role', 'browser', 'url', 'method', 'table_name', 'record_id', 'old_value', 'new_value']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
            $table->string('model_type')->nullable()->after('action');
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            $table->text('old_values')->nullable()->after('model_id');
            $table->text('new_values')->nullable()->after('old_values');
            $table->string('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'model_type', 'model_id', 'old_values', 'new_values', 'user_agent']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('user')->nullable();
            $table->string('role')->nullable();
            $table->string('browser')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->string('table_name')->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
        });
    }
};
