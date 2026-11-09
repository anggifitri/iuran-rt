<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            if (!Schema::hasColumn('wargas', 'rfid_uid')) {
                $table->string('rfid_uid', 64)->nullable()->unique()->after('nik');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            if (Schema::hasColumn('wargas', 'rfid_uid')) {
                $table->dropUnique(['rfid_uid']);
                $table->dropColumn('rfid_uid');
            }
        });
    }
};
