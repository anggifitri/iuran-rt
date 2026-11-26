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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('nik');
            }
            if (!Schema::hasColumn('users', 'privacy_settings')) {
                $table->json('privacy_settings')->nullable()->after('notification_preferences');
            }
            if (!Schema::hasColumn('users', 'theme_preference')) {
                $table->string('theme_preference', 20)->default('light')->after('privacy_settings');
            }
            if (!Schema::hasColumn('users', 'deactivation_requested_at')) {
                $table->timestamp('deactivation_requested_at')->nullable()->after('theme_preference');
            }
            if (!Schema::hasColumn('users', 'deactivation_reason')) {
                $table->text('deactivation_reason')->nullable()->after('deactivation_requested_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('users', 'phone_verified_at')) $cols[] = 'phone_verified_at';
            if (Schema::hasColumn('users', 'notification_preferences')) $cols[] = 'notification_preferences';
            if (Schema::hasColumn('users', 'privacy_settings')) $cols[] = 'privacy_settings';
            if (Schema::hasColumn('users', 'theme_preference')) $cols[] = 'theme_preference';
            if (Schema::hasColumn('users', 'deactivation_requested_at')) $cols[] = 'deactivation_requested_at';
            if (Schema::hasColumn('users', 'deactivation_reason')) $cols[] = 'deactivation_reason';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
