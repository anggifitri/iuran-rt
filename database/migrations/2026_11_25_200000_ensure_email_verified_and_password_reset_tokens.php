<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pastikan tabel password_reset_tokens tersedia untuk Laravel Password Broker
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // 2. Tandai seluruh akun yang sudah ada sebelum fitur ini dipasang sebagai terverifikasi (email_verified_at = now())
        // Guardrail: Hanya pendaftar baru yang wajib melalui proses verifikasi email
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive: tidak membatalkan status verifikasi akun eksisting
    }
};
