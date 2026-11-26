<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'status')) {
                $table->string('status', 30)->default('success')->after('event');
            }
            if (!Schema::hasColumn('audit_logs', 'action_type')) {
                $table->string('action_type', 50)->nullable()->after('status');
            }
            if (!Schema::hasColumn('audit_logs', 'device_info')) {
                $table->string('device_info', 255)->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('audit_logs', 'is_suspicious')) {
                $table->boolean('is_suspicious')->default(false)->after('device_info');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('audit_logs', 'status')) $cols[] = 'status';
            if (Schema::hasColumn('audit_logs', 'action_type')) $cols[] = 'action_type';
            if (Schema::hasColumn('audit_logs', 'device_info')) $cols[] = 'device_info';
            if (Schema::hasColumn('audit_logs', 'is_suspicious')) $cols[] = 'is_suspicious';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
