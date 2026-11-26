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
        if (Schema::hasTable('wargas') && !Schema::hasColumn('wargas', 'user_id')) {
            Schema::table('wargas', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->index();
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'warga_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('warga_id')->nullable()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('wargas') && Schema::hasColumn('wargas', 'user_id')) {
            Schema::table('wargas', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'warga_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('warga_id');
            });
        }
    }
};
