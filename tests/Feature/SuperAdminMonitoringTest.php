<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminRw;
    protected User $adminRt;
    protected User $warga;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Super Admin HQ',
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
        ]);

        $this->adminRt = User::create([
            'name' => 'Admin RT 006',
            'email' => 'adminrt@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'admin_rt',
            'rt_number' => '006',
            'status' => 'active',
            'rw_number' => '018',
        ]);

        $this->warga = User::create([
            'name' => 'Warga Wawan',
            'email' => 'warga@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'rt_number' => '006',
            'status' => 'active',
            'rw_number' => '018',
        ]);
    }

    /**
     * Test 1: Guest is redirected to login
     */
    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/super-admin/monitoring');
        $response->assertRedirect('/login');
    }

    /**
     * Test 2: Admin RW cannot access super-admin monitoring
     */
    public function test_admin_rw_cannot_access_monitoring()
    {
        $response = $this->actingAs($this->adminRw)->get('/super-admin/monitoring');
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    /**
     * Test 3: Admin RT cannot access super-admin monitoring
     */
    public function test_admin_rt_cannot_access_monitoring()
    {
        $response = $this->actingAs($this->adminRt)->get('/super-admin/monitoring');
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    /**
     * Test 4: Warga cannot access super-admin monitoring
     */
    public function test_warga_cannot_access_monitoring()
    {
        $response = $this->actingAs($this->warga)->get('/super-admin/monitoring');
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    /**
     * Test 5: Super Admin can access monitoring and render all 9 main sections
     */
    public function test_super_admin_can_access_monitoring_and_render_all_sections()
    {
        $response = $this->actingAs($this->superAdmin)->get('/super-admin/monitoring');

        $response->assertStatus(200);
        $response->assertSee('Pusat Kendali Monitoring Sistem &amp; Infrastruktur', false);
        $response->assertSee('Ringkasan Kesehatan Platform (System Health Overview)');
        $response->assertSee('Server Status');
        $response->assertSee('Database Health');
        $response->assertSee('Active Users');
        $response->assertSee('Storage Usage');
        $response->assertSee('API &amp; Services', false);
        $response->assertSee('Backup Status');
        $response->assertSee('Metrik Performa Server Aktual');
        $response->assertSee('Diagnostik Database &amp; Konektivitas', false);
        $response->assertSee('Runtime &amp; Ekstensi PHP', false);
        $response->assertSee('Kondisi Modul Aplikasi NexaNest (22 Modul Terdaftar)');
        $response->assertSee('Pemantauan Keamanan &amp; Audit Percobaan Akses', false);
        $response->assertSee('Status Cadangan Database &amp; Pemulihan', false);
        $response->assertSee('Monitoring Perangkat IoT &amp; RFID Akses (Persiapan Integrasi)', false);
        $response->assertSee('Belum ada perangkat terhubung');
        $response->assertSee('Riwayat Kejadian &amp; Insiden Sistem (System Incident History)', false);

        // Verifikasi 5/5 API & Services dan Profil Saya Operational
        $viewData = $response->viewData('health');
        $this->assertEquals(5, $viewData['core_routes_count']);
        $this->assertEquals(5, $viewData['core_routes_total']);
        $this->assertStringEndsWith('WIB', $viewData['server_time']);
        $this->assertStringEndsWith('WIB', $response->viewData('last_inspected_at'));

        $modules = collect($response->viewData('modules'));
        $profilSaya = $modules->firstWhere('key', 'profil_saya');
        $this->assertNotNull($profilSaya);
        $this->assertEquals('Operational', $profilSaya['status']);
        $this->assertEquals('normal', $profilSaya['level']);
    }

    /**
     * Test 6: API metrics endpoint returns JSON for Super Admin
     */
    public function test_api_metrics_endpoint_returns_json_for_super_admin()
    {
        $response = $this->actingAs($this->superAdmin)->getJson('/super-admin/monitoring/api/metrics');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'health',
                'performance',
                'database',
                'runtime',
                'modules',
                'security',
                'backup',
                'iot',
                'incidents',
                'last_inspected_at',
            ],
            'timestamp',
        ]);
    }

    /**
     * Test 7: Integrity check endpoint works and redirects back
     */
    public function test_integrity_check_endpoint_works()
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/monitoring/integrity-check');

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    /**
     * Test 8: Incident filter works
     */
    public function test_incident_filter_works()
    {
        $response = $this->actingAs($this->superAdmin)->get('/super-admin/monitoring?filter=critical');

        $response->assertStatus(200);
    }
}
