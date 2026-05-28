<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_r_t_s', function (Blueprint $table) {
            $table->id();
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('kas_r_t_s')->insert([
            'balance' => 0,
            'total_income' => 0,
            'total_expense' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_r_t_s');
    }
};
