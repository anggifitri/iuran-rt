<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            DB::statement('
                CREATE TABLE "users_new" (
                    "id" integer primary key autoincrement not null,
                    "name" varchar not null,
                    "email" varchar not null,
                    "email_verified_at" datetime,
                    "password" varchar not null,
                    "role" varchar not null default \'warga\',
                    "rt_number" varchar,
                    "phone" varchar,
                    "address" text,
                    "remember_token" varchar,
                    "created_at" datetime,
                    "updated_at" datetime,
                    "profile_photo" varchar,
                    "no_kk" varchar,
                    "nik" varchar,
                    "phone_verified_at" datetime,
                    "notification_preferences" text,
                    "privacy_settings" text,
                    "theme_preference" varchar not null default \'light\',
                    "deactivation_requested_at" datetime,
                    "deactivation_reason" text,
                    "rw_number" varchar not null default \'018\',
                    "status" varchar not null default \'active\',
                    "last_active_at" datetime
                )
            ');

            DB::statement('INSERT INTO "users_new" SELECT * FROM "users"');
            DB::statement('DROP TABLE "users"');
            DB::statement('ALTER TABLE "users_new" RENAME TO "users"');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS "users_email_unique" ON "users" ("email")');
            DB::statement('CREATE INDEX IF NOT EXISTS "users_rw_number_index" ON "users" ("rw_number")');
            DB::statement('CREATE INDEX IF NOT EXISTS "users_status_index" ON "users" ("status")');

            DB::statement('PRAGMA foreign_keys=ON');
        } else {
            DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'warga'");
        }
    }

    public function down(): void
    {
        // No-op rollback
    }
};
