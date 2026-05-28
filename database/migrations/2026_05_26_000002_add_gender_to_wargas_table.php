<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->char('gender', 1)->nullable()->after('nik'); // 'L' or 'P'
        });
    }

    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
