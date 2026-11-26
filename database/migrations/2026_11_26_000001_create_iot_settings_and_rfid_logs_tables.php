<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('iot_settings')) {
            Schema::create('iot_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_active')->default(true);
                $table->string('api_token');
                $table->string('gate_name')->default('Gerbang Utama RW');
                $table->decimal('gate_latitude', 10, 7)->nullable();
                $table->decimal('gate_longitude', 10, 7)->nullable();
                $table->integer('geofence_radius')->default(20);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rfid_logs')) {
            Schema::create('rfid_logs', function (Blueprint $table) {
                $table->id();
                $table->string('uid')->index();
                $table->foreignId('warga_id')->nullable()->constrained('wargas')->nullOnDelete();
                $table->string('status'); // registered, unregistered, rejected_token, api_disabled
                $table->string('context')->default('general'); // rekam_ktp, smart_gate, general
                $table->string('method')->default('rfid'); // rfid, gps_emergency, simulation
                $table->string('device_ip')->nullable();
                $table->string('message')->nullable();
                $table->text('raw_payload')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rfid_logs');
        Schema::dropIfExists('iot_settings');
    }
};
