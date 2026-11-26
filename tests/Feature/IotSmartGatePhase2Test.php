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

class IotSmartGatePhase2Test extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminRw;
    protected User $adminRt;
    protected User $wargaUser;
    protected Warga $warga;
    protected IotSetting $setting;

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

        $this->adminRt = User::create([
            'name' => 'Admin RT 006',
            'email' => 'adminrt@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'admin_rt',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
        ]);

        $this->wargaUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
            'nik' => '3276011234567890',
        ]);

        $this->warga = Warga::create([
            'nama' => 'Budi Santoso',
            'nik' => '3276011234567890',
            'rt_number' => '006',
            'rw_number' => '018',
            'blok_rumah' => 'A1/05',
            'alamat' => 'Jl. Kenanga No. 5',
            'is_kk' => true,
            'tanggal_lahir' => '1990-01-01',
            'rfid_uid' => 'KTPBUDI8899AA',
        ]);

        // Initialize IoT Setting
        $this->setting = IotSetting::current();
        $this->setting->update([
            'is_active' => true,
            'gate_name' => 'Gerbang Utama RW 018',
            'gate_latitude' => -6.3888890,
            'gate_longitude' => 106.8455560,
            'geofence_radius' => 20,
        ]);
    }

    public function test_smart_gate_page_accessible_by_all_authenticated_roles()
    {
        // Unauthenticated -> 302 to login
        $response = $this->get('/smart-gate');
        $response->assertRedirect('/login');

        // Super Admin -> 200
        $responseSa = $this->actingAs($this->superAdmin)->get('/smart-gate');
        $responseSa->assertStatus(200)->assertSee('Gerbang Utama RW 018');

        // Admin RW -> 200
        $responseRw = $this->actingAs($this->adminRw)->get('/smart-gate');
        $responseRw->assertStatus(200)->assertSee('Gerbang Utama RW 018');

        // Admin RT -> 200
        $responseRt = $this->actingAs($this->adminRt)->get('/smart-gate');
        $responseRt->assertStatus(200)->assertSee('Gerbang Utama RW 018');

        // Warga -> 200
        $responseWarga = $this->actingAs($this->wargaUser)->get('/smart-gate');
        $responseWarga->assertStatus(200)->assertSee('Buka Portal Darurat (GPS)');
    }

    public function test_live_status_reports_standby_when_no_recent_events()
    {
        $response = $this->actingAs($this->wargaUser)->getJson('/smart-gate/live-status');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'gate_state' => 'standby',
            ]);
    }

    public function test_live_status_reports_opened_on_recent_registered_tap()
    {
        RfidLog::create([
            'uid' => $this->warga->rfid_uid,
            'warga_id' => $this->warga->id,
            'status' => 'registered',
            'context' => 'smart_gate',
            'method' => 'rfid',
            'device_ip' => '192.168.1.100',
            'message' => 'Kartu terdaftar atas nama Budi Santoso',
        ]);

        $response = $this->actingAs($this->wargaUser)->getJson('/smart-gate/live-status');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'gate_state' => 'opened',
            ]);

        $this->assertStringContainsString('Budi Santoso', $response->json('gate_notice.message'));
    }

    public function test_live_status_reports_rejected_on_recent_unregistered_tap()
    {
        RfidLog::create([
            'uid' => 'UNKNOWN9988',
            'warga_id' => null,
            'status' => 'unregistered',
            'context' => 'smart_gate',
            'method' => 'rfid',
            'device_ip' => '192.168.1.100',
            'message' => 'Kartu tidak terdaftar',
        ]);

        $response = $this->actingAs($this->wargaUser)->getJson('/smart-gate/live-status');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'gate_state' => 'rejected',
            ]);
    }

    public function test_emergency_open_validates_coordinates()
    {
        $response = $this->actingAs($this->wargaUser)->postJson('/smart-gate/emergency-open', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'longitude']);
    }

    public function test_emergency_open_fails_when_gate_coordinates_not_configured()
    {
        $this->setting->update([
            'gate_latitude' => null,
            'gate_longitude' => null,
        ]);

        $response = $this->actingAs($this->wargaUser)->postJson('/smart-gate/emergency-open', [
            'latitude' => -6.3888890,
            'longitude' => 106.8455560,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertStringContainsString('belum diatur', $response->json('message'));
    }

    public function test_emergency_open_succeeds_within_geofence_radius()
    {
        // Posisi pengguna sama persis dengan gerbang (Jarak = 0 meter <= 20 meter radius)
        $response = $this->actingAs($this->wargaUser)->postJson('/smart-gate/emergency-open', [
            'latitude' => -6.3888890,
            'longitude' => 106.8455560,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'radius' => 20,
            ]);

        $this->assertDatabaseHas('rfid_logs', [
            'uid' => 'GPS-EMERGENCY',
            'status' => 'registered',
            'method' => 'gps_emergency',
        ]);

        // Cek live status otomatis ikut terbuka
        $live = $this->actingAs($this->wargaUser)->getJson('/smart-gate/live-status');
        $live->assertStatus(200)
            ->assertJson([
                'gate_state' => 'opened',
            ]);
    }

    public function test_emergency_open_rejected_when_outside_geofence_radius()
    {
        // Posisi pengguna berjarak ~500 meter dari gerbang
        // -6.3888890 + 0.005 ~= 550 meter
        $farLatitude = -6.3888890 + 0.005;

        $response = $this->actingAs($this->wargaUser)->postJson('/smart-gate/emergency-open', [
            'latitude' => $farLatitude,
            'longitude' => 106.8455560,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'radius' => 20,
            ]);

        $this->assertDatabaseHas('rfid_logs', [
            'uid' => 'GPS-EMERGENCY',
            'status' => 'rejected_geofence',
            'method' => 'gps_emergency',
        ]);

        // Cek live status menampilkan rejected
        $live = $this->actingAs($this->wargaUser)->getJson('/smart-gate/live-status');
        $live->assertStatus(200)
            ->assertJson([
                'gate_state' => 'rejected',
            ]);
    }

    public function test_rfid_scan_registered_card_immediately_triggers_gate_open_in_live_status()
    {
        $responseScan = $this->postJson('/api/rfid/scan', [
            'uid' => $this->warga->rfid_uid,
            'token' => $this->setting->api_token,
            'device_id' => 'ESP32-GATE-MAIN',
            'context' => 'smart_gate',
        ]);

        $responseScan->assertStatus(200)
            ->assertJson([
                'success' => true,
                'registered' => true,
                'uid' => $this->warga->rfid_uid,
            ]);

        $live = $this->actingAs($this->wargaUser)->getJson('/smart-gate/live-status');
        $live->assertStatus(200)
            ->assertJson([
                'gate_state' => 'opened',
            ]);

        $this->assertStringContainsString('Budi Santoso', $live->json('gate_notice.message'));
    }
}
