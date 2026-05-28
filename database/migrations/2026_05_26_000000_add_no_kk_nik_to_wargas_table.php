<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->string('no_kk')->nullable()->after('nomor_hp');
            $table->string('nik')->nullable()->after('no_kk');
        });
    }

    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropColumn(['no_kk', 'nik']);
        });
    }
};
