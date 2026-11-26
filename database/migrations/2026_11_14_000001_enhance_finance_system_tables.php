<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance transactions table
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'nomor_transaksi')) {
                    $table->string('nomor_transaksi', 50)->nullable()->after('id');
                }
                if (!Schema::hasColumn('transactions', 'sumber_tujuan')) {
                    $table->string('sumber_tujuan')->nullable()->after('keterangan');
                }
                if (!Schema::hasColumn('transactions', 'metode_pembayaran')) {
                    $table->string('metode_pembayaran')->default('Tunai')->after('sumber_tujuan');
                }
                if (!Schema::hasColumn('transactions', 'nomor_referensi')) {
                    $table->string('nomor_referensi')->nullable()->after('metode_pembayaran');
                }
                if (!Schema::hasColumn('transactions', 'bukti_transaksi')) {
                    $table->string('bukti_transaksi')->nullable()->after('nomor_referensi');
                }
                if (!Schema::hasColumn('transactions', 'status')) {
                    $table->string('status', 25)->default('selesai')->after('bukti_transaksi');
                }
                if (!Schema::hasColumn('transactions', 'pembayaran_id')) {
                    $table->unsignedBigInteger('pembayaran_id')->nullable()->after('status');
                }
                if (!Schema::hasColumn('transactions', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('pembayaran_id');
                }
            });
        }

        // 2. Enhance iurans table
        if (Schema::hasTable('iurans')) {
            Schema::table('iurans', function (Blueprint $table) {
                if (!Schema::hasColumn('iurans', 'periode')) {
                    $table->string('periode')->default('bulanan')->after('amount');
                }
                if (!Schema::hasColumn('iurans', 'target_wilayah')) {
                    $table->string('target_wilayah')->default('all')->after('periode');
                }
                if (!Schema::hasColumn('iurans', 'target_pembayar')) {
                    $table->string('target_pembayar')->default('kk')->after('target_wilayah');
                }
                if (!Schema::hasColumn('iurans', 'tanggal_mulai')) {
                    $table->date('tanggal_mulai')->nullable()->after('target_pembayar');
                }
            });
        }

        // 3. Enhance pembayarans table
        if (Schema::hasTable('pembayarans')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                if (!Schema::hasColumn('pembayarans', 'warga_id')) {
                    $table->unsignedBigInteger('warga_id')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('pembayarans', 'rt_number')) {
                    $table->string('rt_number', 3)->nullable()->after('warga_id');
                }
                if (!Schema::hasColumn('pembayarans', 'bulan')) {
                    $table->integer('bulan')->nullable()->after('payment_date');
                }
                if (!Schema::hasColumn('pembayarans', 'tahun')) {
                    $table->integer('tahun')->nullable()->after('bulan');
                }
                if (!Schema::hasColumn('pembayarans', 'nominal_tagihan')) {
                    $table->decimal('nominal_tagihan', 12, 2)->nullable()->after('amount_paid');
                }
                if (!Schema::hasColumn('pembayarans', 'transaction_id')) {
                    $table->unsignedBigInteger('transaction_id')->nullable()->after('confirmed_by');
                }
            });
        }
    }

    public function down(): void
    {
        // Safe down
    }
};
