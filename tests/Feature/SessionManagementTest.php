<?php

namespace Tests\Feature;

use App\Models\PlatformSetting;
use App\Models\User;
use App\Models\Warga;
use App\Models\WilayahRw;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SessionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminRw;
    protected User $wargaUser;
    protected Warga $warga;
    protected WilayahRw $rw;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Wilayah RW 018
        $this->rw = WilayahRw::create([
            'rw_number' => '018',
            'tenant_id' => 'TENANT-RW018-MEKARJAYA',
            'nama_wilayah' => 'RW 018 Kelurahan Mekarjaya',
            'kelurahan' => 'Kelurahan Mekarjaya',
            'kecamatan' => 'Kecamatan Sukmajaya',
            'kota' => 'Kota Depok',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '16411',
            'alamat_sekretariat' => 'Balai Warga RW 018',
            'status' => 'active',
            'total_rt' => 5,
        ]);

        // 2. Setup Super Admin
        $this->superAdmin = User::create([
            'name' => 'Super Admin HQ',
            'email' => 'superadmin@nexanest.test',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'status' => 'active',
            'rw_number' => '018',
        ]);

        // 3. Setup Admin RW
        $this->adminRw = User::create([
            'name' => 'Dirga Pradipta',
            'email' => 'dirga@nexanest.test',
            'password' => Hash::make('password123'),
            'role' => 'admin_rw',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '000',
        ]);

        // 4. Setup Warga User
        $this->wargaUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@nexanest.test',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '007',
        ]);

        // 5. Setup Warga Record
        $this->warga = Warga::create([
            'user_id' => $this->wargaUser->id,
            'nama' => 'Budi Santoso',
            'nik' => '3276011122330001',
            'no_kk' => '3276011122339999',
            'blok_rumah' => 'B2/07',
            'nomor_rumah' => '12',
            'alamat' => 'Jl. Boulevard No. 12',
            'rt_number' => '007',
            'rw_number' => '018',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'pekerjaan' => 'Karyawan Swasta',
            'status_keluarga' => 'Kepala Keluarga',
            'status_tinggal' => 'Tetap',
            'status_verifikasi' => 'verified',
        ]);
    }

    /**
     * 1. Login Warga tanpa Remember Me
     */
    public function test_login_warga_without_remember_me()
    {
        $response = $this->post('/login', [
            'email' => 'budi@nexanest.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->wargaUser);

        // Periksa expiry session & remember token
        $this->wargaUser->refresh();
        $this->assertNull($this->wargaUser->remember_token);

        // Periksa session anchors
        $this->assertNotEmpty(session('login_time'));
        $this->assertNotEmpty(session('last_activity_time'));
        $this->assertFalse(session('remember_me'));
        $this->assertNotEmpty(session('access_token'));
        $this->assertNotEmpty(session('refresh_token'));
    }

    /**
     * 2. Login Warga dengan Remember Me ketika diizinkan
     */
    public function test_login_warga_with_remember_me_when_enabled()
    {
        PlatformSetting::set('allow_remember_me', '1');

        $response = $this->post('/login', [
            'email' => 'budi@nexanest.test',
            'password' => 'password123',
            'remember' => '1',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->wargaUser);

        $this->wargaUser->refresh();
        $this->assertNotNull($this->wargaUser->remember_token);
        $this->assertTrue(session('remember_me'));
    }

    /**
     * 3. Fitur Remember Me diabaikan jika dinonaktifkan di Platform Settings
     */
    public function test_remember_me_ignored_when_disabled_by_platform_settings()
    {
        PlatformSetting::set('allow_remember_me', '0');

        $response = $this->post('/login', [
            'email' => 'budi@nexanest.test',
            'password' => 'password123',
            'remember' => '1',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->wargaUser);

        $this->wargaUser->refresh();
        $this->assertNull($this->wargaUser->remember_token);
        $this->assertFalse(session('remember_me'));
    }

    /**
     * 4. User aktif sebelum batas Idle Timeout: last_activity_time diperbarui
     */
    public function test_active_user_within_idle_timeout_updates_last_activity()
    {
        $this->actingAs($this->wargaUser);

        $initialTime = time() - 600; // 10 menit lalu
        session([
            'login_time' => $initialTime,
            'last_activity_time' => $initialTime,
        ]);

        $response = $this->get('/dashboard');
        $response->assertOk();

        $this->assertAuthenticatedAs($this->wargaUser);
        $this->assertGreaterThan($initialTime, session('last_activity_time'));
    }

    /**
     * 5. Inaktivitas melebihi batas Idle Timeout (30 menit) -> diputus dan logout otomatis
     */
    public function test_idle_timeout_terminates_session_after_30_minutes_inactivity()
    {
        PlatformSetting::set('session_idle_timeout_minutes', '30');

        $this->actingAs($this->wargaUser);

        // Simulasi inaktif selama 31 menit
        $pastTime = time() - (31 * 60);
        session([
            'login_time' => $pastTime,
            'last_activity_time' => $pastTime,
        ]);

        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
        $response->assertSessionHas('error');

        $this->assertGuest();
    }

    /**
     * 6. Background Polling Request tidak memperpanjang Idle Timeout
     */
    public function test_background_polling_does_not_extend_idle_timeout()
    {
        $this->actingAs($this->wargaUser);

        $tenMinutesAgo = time() - 600;
        session([
            'login_time' => time() - 1200,
            'last_activity_time' => $tenMinutesAgo,
        ]);

        // Request dengan header X-Background-Request
        $response = $this->withHeader('X-Background-Request', '1')->get('/dashboard');
        $response->assertOk();

        // last_activity_time TIDAK boleh diperbarui ke now()
        $this->assertEquals($tenMinutesAgo, session('last_activity_time'));
    }

    /**
     * 7. Absolute Timeout: diputus setelah 60 menit sejak login awal meski user terus aktif
     */
    public function test_absolute_timeout_terminates_session_after_60_minutes_even_if_active()
    {
        PlatformSetting::set('session_absolute_timeout_minutes', '60');

        $this->actingAs($this->wargaUser);

        // Login 61 menit lalu, tapi baru saja aktif 1 menit lalu
        $loginTime = time() - (61 * 60);
        $recentActivity = time() - 60;

        session([
            'login_time' => $loginTime,
            'last_activity_time' => $recentActivity,
        ]);

        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
        $response->assertSessionHas('error');

        $this->assertGuest();
    }

    /**
     * 8. Akun suspended / dinonaktifkan: sesi langsung diputus saat request berikutnya
     */
    public function test_suspended_user_is_immediately_terminated_on_next_request()
    {
        $this->actingAs($this->wargaUser);

        session([
            'login_time' => time(),
            'last_activity_time' => time(),
        ]);

        // Admin menangguhkan akun di database
        $this->wargaUser->update(['status' => 'suspended']);

        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
        $response->assertSessionHas('error');

        $this->assertGuest();
    }

    /**
     * 9. Refresh Token berhasil dan melakukan Refresh Token Rotation dalam batas waktu
     */
    public function test_refresh_token_succeeds_and_rotates_token()
    {
        $this->actingAs($this->wargaUser);

        $oldRefreshToken = 'REFRESH_TEST_TOKEN_' . bin2hex(random_bytes(16));
        session([
            'login_time' => time() - 600, // 10 menit lalu (masih < 60 menit absolute)
            'last_activity_time' => time() - 100,
            'refresh_token' => $oldRefreshToken,
            'refresh_token_user_id' => $this->wargaUser->id,
        ]);

        $response = $this->postJson('/api/auth/token/refresh', [
            'refresh_token' => $oldRefreshToken,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'access_token',
            'refresh_token',
            'expires_in',
            'token_type',
        ]);

        $newRefreshToken = $response->json('refresh_token');
        $this->assertNotEquals($oldRefreshToken, $newRefreshToken);

        // Uji Token Rotation: Token lama sudah tidak bisa dipakai lagi
        $replayResponse = $this->postJson('/api/auth/token/refresh', [
            'refresh_token' => $oldRefreshToken,
        ]);
        $replayResponse->assertStatus(401);
    }

    /**
     * 10. Refresh Token ditolak jika Absolute Timeout (60 menit) terlampaui
     */
    public function test_refresh_token_rejected_when_absolute_timeout_exceeded()
    {
        $this->actingAs($this->wargaUser);

        $refreshToken = 'REFRESH_TEST_' . bin2hex(random_bytes(16));
        session([
            'login_time' => time() - (61 * 60), // Sudah 61 menit lalu
            'last_activity_time' => time() - 100,
            'refresh_token' => $refreshToken,
            'refresh_token_user_id' => $this->wargaUser->id,
        ]);

        $response = $this->postJson('/api/auth/token/refresh', [
            'refresh_token' => $refreshToken,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'error' => 'session_absolute_timeout',
        ]);
    }

    /**
     * 11. Endpoint Status Sesi mengembalikan sisa waktu idle & absolute yang akurat
     */
    public function test_session_status_endpoint_returns_accurate_lifetimes()
    {
        PlatformSetting::set('session_idle_timeout_minutes', '30');
        PlatformSetting::set('session_absolute_timeout_minutes', '60');

        $this->actingAs($this->wargaUser);

        $now = time();
        session([
            'login_time' => $now - 600, // 10 menit lalu -> sisa absolute ~50 menit (3000 detik)
            'last_activity_time' => $now - 300, // 5 menit lalu -> sisa idle ~25 menit (1500 detik)
            'remember_me' => true,
        ]);

        $response = $this->getJson('/api/auth/session/status');
        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'authenticated',
            'user' => ['id', 'name', 'role'],
            'session' => [
                'idle_timeout_minutes',
                'idle_remaining_seconds',
                'absolute_timeout_minutes',
                'absolute_remaining_seconds',
                'is_remember_me',
            ]
        ]);

        $sessionData = $response->json('session');
        $this->assertLessThanOrEqual(1500, $sessionData['idle_remaining_seconds']);
        $this->assertGreaterThan(1400, $sessionData['idle_remaining_seconds']);
        $this->assertLessThanOrEqual(3000, $sessionData['absolute_remaining_seconds']);
        $this->assertGreaterThan(2900, $sessionData['absolute_remaining_seconds']);
    }

    /**
     * 12. Logout mencabut sesi, token akses, dan remember token
     */
    public function test_logout_revokes_session_and_remember_token()
    {
        $this->wargaUser->update(['remember_token' => 'VALID_REMEMBER_TOKEN']);
        $this->actingAs($this->wargaUser);

        $response = $this->post('/logout');
        $response->assertRedirect('/');

        $this->assertGuest();
        $this->wargaUser->refresh();
        $this->assertNull($this->wargaUser->remember_token);
    }

    /**
     * 13. Super Admin Role Switcher mempertahankan anchor login_time awal tanpa logout
     */
    public function test_role_switcher_preserves_initial_login_time_anchor_without_logout()
    {
        $this->actingAs($this->superAdmin);

        $initialLoginTime = time() - 900; // 15 menit lalu
        session([
            'login_time' => $initialLoginTime,
            'last_activity_time' => $initialLoginTime,
        ]);

        // Super Admin beralih role ke Admin RW
        $response = $this->post('/super-admin/role-switch', [
            'target_role' => 'admin_rw',
            'target_rw' => '018',
            'mode' => 'operational',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->superAdmin);

        // Pastikan role switch aktif di session
        $this->assertTrue(session('role_switch.is_switched'));
        $this->assertEquals('admin_rw', session('role_switch.target_role'));

        // Pastikan anchor login_time asli TIDAK di-reset
        $this->assertEquals($initialLoginTime, session('login_time'));

        // Keluar dari role switch
        $exitResponse = $this->post('/super-admin/role-switch/exit');
        $exitResponse->assertRedirect('/super-admin/dashboard');

        // Pastikan kembali ke Super Admin murni dan login_time tetap utuh
        $this->assertNull(session('role_switch'));
        $this->assertEquals($initialLoginTime, session('login_time'));
        $this->assertAuthenticatedAs($this->superAdmin);
    }

    /**
     * 14. Pembaruan Parameter Keamanan & Sesi di Platform Settings
     */
    public function test_platform_settings_security_updates_session_parameters()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post('/super-admin/settings/security', [
            'session_idle_timeout_minutes' => 45,
            'session_absolute_timeout_minutes' => 90,
            'api_token_lifetime_minutes' => 20,
            'allow_remember_me' => '1',
            'sensitive_data_auth_timeout_minutes' => 15,
            'max_login_attempts' => 5,
            'lockout_duration_minutes' => 15,
            'max_rt_per_rw' => 25,
        ]);

        $response->assertRedirect('/super-admin/settings?tab=security');
        $response->assertSessionHas('success');

        $this->assertEquals('45', PlatformSetting::get('session_idle_timeout_minutes'));
        $this->assertEquals('90', PlatformSetting::get('session_absolute_timeout_minutes'));
        $this->assertEquals('20', PlatformSetting::get('api_token_lifetime_minutes'));
        $this->assertEquals('1', PlatformSetting::get('allow_remember_me'));
    }
}
