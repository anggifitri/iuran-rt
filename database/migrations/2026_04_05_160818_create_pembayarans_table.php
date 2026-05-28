<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('iuran_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 12, 2);
            $table->date('payment_date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->string('payment_method')->default('cash');
            $table->string('proof_image')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
