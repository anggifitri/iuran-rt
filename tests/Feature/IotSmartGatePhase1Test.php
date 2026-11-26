<?php

namespace Tests\Feature;

use App\Models\IotSetting;
use App\Models\RfidLog;
use App\Models\User;
use App\Models\Warga;
use App\Models\WilayahRw;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class IotSmartGatePhase1Test extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminRw;
    protected User $wargaUser;
    protected Warga $warga;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        // Wilayah RW
        WilayahRw::create([
            'rw_number' => '018',
            'nama_wilayah' => 'RW 018 Kelurahan Mekarjaya',
            'status' => 'active',
        ]);

        // Users
        $this->superAdmin = User::create([
            'name' => 'Anggi Fitri',
            'email' => 'superadmin@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'status' => 'active',
            'rw_number' => '018',
        ]);

        $this->adminRw = User::create([
            'name' => 'Admin RW 018',
            'email' => 'adminrw@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'admin_rw',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '000',
        ]);

        $this->wargaUser = User::create([
            'name' => 'Warga Test',
            'email' => 'warga@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
        ]);

        // Warga record
        $this->warga = Warga::create([
            'nama' => 'Budi Santoso',
            'nik' => '3276011234567890',
            'rt_number' => '006',
            'rw_number' => '018',
            'blok_rumah' => 'A1/05',
            'alamat' => 'Jl. Kenanga No. 5',
            'is_kk' => true,
            'tanggal_lahir' => '1990-01-01',
            'rfid_uid' => 'KTPBUDI123456',
        ]);
    }

    public function test_iot_setting_and_rfid_log_models_and_tables_exist()
    {
        $setting = IotSetting::current();
        $this->assertNotNull($setting);
        $this->assertTrue($setting->is_active);
        $this->assertNotEmpty($setting->api_token);
        $this->assertEquals(32, strlen($setting->api_token));
        $this->assertEquals('Gerbang Utama RW', $setting->gate_name);
    }

    public function test_api_rfid_scan_validates_required_uid()
    {
        $response = $this->postJson('/api/rfid/scan', [
            'token' => IotSetting::current()->api_token,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'error',
            ]);
    }

    public function test_api_rfid_scan_rejects_missing_or_invalid_token()
    {
        $response = $this->postJson('/api/rfid/scan', [
            'uid' => 'A1B2C3D4',
            'token' => 'invalid-token-12345',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'status' => 'rejected_token',
                'message' => 'Token API tidak valid',
            ]);

        $this->assertDatabaseHas('rfid_logs', [
            'uid' => 'A1B2C3D4',
            'status' => 'rejected_token',
        ]);
    }

    public function test_api_rfid_scan_rejects_when_api_is_disabled()
    {
        $setting = IotSetting::current();
        $setting->update(['is_active' => false]);

        $response = $this->postJson('/api/rfid/scan', [
            'uid' => 'A1B2C3D4',
            'token' => $setting->api_token,
        ]);

        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
                'status' => 'api_disabled',
            ]);

        $this->assertDatabaseHas('rfid_logs', [
            'uid' => 'A1B2C3D4',
            'status' => 'api_disabled',
        ]);

        $setting->update(['is_active' => true]);
    }

    public function test_api_rfid_scan_success_unregistered_card()
    {
        $setting = IotSetting::current();
        $setting->update(['is_active' => true]);

        $uniqueUid = 'UNREG' . rand(1000, 9999);

        $response = $this->postJson('/api/rfid/scan', [
            'uid' => $uniqueUid,
            'token' => $setting->api_token,
            'device_id' => 'ESP32-GATE-TEST',
            'context' => 'smart_gate',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'success',
                'registered' => false,
                'uid' => $uniqueUid,
            ]);

        $this->assertDatabaseHas('rfid_logs', [
            'uid' => $uniqueUid,
            'status' => 'unregistered',
            'context' => 'smart_gate',
        ]);

        // Cek bahwa cache rfid_last_scan terisi untuk modal rekam E-KTP
        $cached = Cache::get('rfid_last_scan');
        $this->assertNotNull($cached);
        $this->assertEquals($uniqueUid, $cached['uid']);
    }

    public function test_api_rfid_scan_success_registered_warga_card()
    {
        $setting = IotSetting::current();
        $setting->update(['is_active' => true]);

        $registeredUid = $this->warga->rfid_uid;

        $response = $this->postJson('/api/rfid/scan', [
            'uid' => $registeredUid,
            'token' => $setting->api_token,
            'device_id' => 'ESP32-GATE-TEST',
            'context' => 'smart_gate',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'success',
                'registered' => true,
                'uid' => $registeredUid,
                'warga' => [
                    'id' => $this->warga->id,
                    'nama' => 'Budi Santoso',
                    'rt_number' => '006',
                ],
            ]);

        $this->assertDatabaseHas('rfid_logs', [
            'uid' => $registeredUid,
            'status' => 'registered',
            'warga_id' => $this->warga->id,
        ]);
    }

    public function test_admin_simulation_passes_without_explicit_token()
    {
        $uniqueUid = 'SIM' . rand(1000, 9999);

        $response = $this->actingAs($this->superAdmin)->postJson('/api/rfid/scan', [
            'uid' => $uniqueUid,
            'device_id' => 'NODEMCU-ESP32-RC522',
            'context' => 'rekam_ktp',
            'method' => 'simulation',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'success',
                'uid' => $uniqueUid,
            ]);

        $this->assertDatabaseHas('rfid_logs', [
            'uid' => $uniqueUid,
            'method' => 'simulation',
        ]);
    }

    public function test_access_control_iot_dashboard()
    {
        $response = $this->actingAs($this->wargaUser)->get('/iot');
        $response->assertStatus(403);

        $responseRw = $this->actingAs($this->adminRw)->get('/iot');
        $responseRw->assertStatus(200);
        $responseRw->assertSee('Pusat Kendali IoT &amp; Smart Gate', false);

        $responseSa = $this->actingAs($this->superAdmin)->get('/iot');
        $responseSa->assertStatus(200);
        $responseSa->assertSee('Pusat Kendali IoT &amp; Smart Gate', false);
    }

    public function test_update_iot_settings()
    {
        $response = $this->actingAs($this->superAdmin)->put('/iot/settings', [
            'is_active' => 1,
            'gate_name' => 'Gerbang Pos Barat RW 018',
            'api_token' => 'TEST_TOKEN_32_CHAR_LONG_ABCDEF1',
            'gate_latitude' => -6.3900000,
            'gate_longitude' => 106.8400000,
            'geofence_radius' => 35,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('iot_settings', [
            'gate_name' => 'Gerbang Pos Barat RW 018',
            'geofence_radius' => 35,
        ]);
    }

    public function test_generate_token_endpoint()
    {
        $response = $this->actingAs($this->superAdmin)->postJson('/iot/generate-token');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'token']);

        $this->assertEquals(32, strlen($response->json('token')));
    }

    public function test_logs_polling_endpoint()
    {
        $response = $this->actingAs($this->superAdmin)->getJson('/iot/logs');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }
}
