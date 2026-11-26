<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\WilayahRw;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminRw;
    protected User $wargaUser;
    protected WilayahRw $rw;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Wilayah RW 018
        $this->rw = WilayahRw::create([
            'rw_number' => '018',
            'tenant_id' => 'TENANT-RW018-MEKARJAYA',
            'nama_wilayah' => 'RW 018 Kelurahan Mekarjaya',
            'kelurahan' => 'Kelurahan Mekarjaya',
            'kecamatan' => 'Kecamatan Sukmajaya',
            'kota' => 'Kota Depok',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '16411',
            'alamat_sekretariat' => 'Balai Warga RW 018, Jl. Boulevard Utama No. 01',
            'status' => 'active',
            'total_rt' => 5,
        ]);

        // Setup Super Admin
        $this->superAdmin = User::create([
            'name' => 'Anggi Fitri',
            'display_name' => 'Anggi HQ',
            'email' => 'superadmin@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'status' => 'active',
            'rw_number' => '018',
            'phone' => '081234567890',
            'bio' => 'Platform Architect & Lead Administrator NexaNest',
        ]);

        // Setup Admin RW
        $this->adminRw = User::create([
            'name' => 'Dirga Pradipta',
            'email' => 'dirga@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'admin_rw',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '000',
        ]);

        // Setup Warga
        $this->wargaUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
        ]);
    }

    public function test_super_admin_can_access_profile_page_and_all_tabs()
    {
        $tabs = ['personal', 'account', 'security', 'notifications', 'activity', 'preferences'];

        foreach ($tabs as $tab) {
            $response = $this->actingAs($this->superAdmin)->get(route('super_admin.profile.index', ['tab' => $tab]));
            $response->assertStatus(200);
            $response->assertSee('Profil Super Admin');
            $response->assertSee('Anggi HQ');
        }
    }

    public function test_non_super_admin_is_forbidden_from_super_admin_profile()
    {
        $responseAdminRw = $this->actingAs($this->adminRw)->get(route('super_admin.profile.index'));
        $responseAdminRw->assertStatus(403);

        $responseWarga = $this->actingAs($this->wargaUser)->get(route('super_admin.profile.index'));
        $responseWarga->assertStatus(403);
    }

    public function test_super_admin_can_update_personal_info_and_avatar()
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $payload = [
            'name' => 'Anggi Fitri Updated',
            'display_name' => 'Anggi Chief HQ',
            'phone' => '089988776655',
            'bio' => 'Updated bio description for Super Admin HQ.',
            'profile_photo' => $avatar,
        ];

        $response = $this->actingAs($this->superAdmin)
            ->post(route('super_admin.profile.update_personal'), $payload);

        $response->assertRedirect(route('super_admin.profile.index', ['tab' => 'personal']));
        $response->assertSessionHas('success');

        $this->superAdmin->refresh();
        $this->assertEquals('Anggi Fitri Updated', $this->superAdmin->name);
        $this->assertEquals('Anggi Chief HQ', $this->superAdmin->display_name);
        $this->assertEquals('089988776655', $this->superAdmin->phone);
        $this->assertEquals('Updated bio description for Super Admin HQ.', $this->superAdmin->bio);
        $this->assertNotNull($this->superAdmin->profile_photo);
        Storage::disk('public')->assertExists($this->superAdmin->profile_photo);
    }

    public function test_super_admin_can_update_email_with_valid_password()
    {
        // 1. Attempt with wrong password
        $failedResponse = $this->actingAs($this->superAdmin)->post(route('super_admin.profile.update_account'), [
            'email' => 'newsuperadmin@nexanest.test',
            'current_password' => 'wrongpassword',
        ]);

        $failedResponse->assertSessionHasErrors('current_password');
        $this->assertEquals('superadmin@nexanest.test', $this->superAdmin->fresh()->email);

        // 2. Attempt with correct password
        $successResponse = $this->actingAs($this->superAdmin)->post(route('super_admin.profile.update_account'), [
            'email' => 'newsuperadmin@nexanest.test',
            'current_password' => 'password123',
        ]);

        $successResponse->assertRedirect(route('super_admin.profile.index', ['tab' => 'account']));
        $successResponse->assertSessionHas('success');
        $this->assertEquals('newsuperadmin@nexanest.test', $this->superAdmin->fresh()->email);
        $this->assertNotNull($this->superAdmin->fresh()->email_verified_at);
    }

    public function test_super_admin_can_update_password()
    {
        // 1. Attempt with wrong current password
        $failedResponse = $this->actingAs($this->superAdmin)->post(route('super_admin.profile.update_password'), [
            'current_password' => 'wrongcurrentpassword',
            'password' => 'newsecurepass123',
            'password_confirmation' => 'newsecurepass123',
        ]);

        $failedResponse->assertSessionHasErrors('current_password');

        // 2. Attempt with correct current password
        $successResponse = $this->actingAs($this->superAdmin)->post(route('super_admin.profile.update_password'), [
            'current_password' => 'password123',
            'password' => 'newsecurepass123',
            'password_confirmation' => 'newsecurepass123',
        ]);

        $successResponse->assertRedirect(route('super_admin.profile.index', ['tab' => 'security']));
        $successResponse->assertSessionHas('success');

        $this->assertTrue(Hash::check('newsecurepass123', $this->superAdmin->fresh()->password));
    }

    public function test_super_admin_can_revoke_other_session()
    {
        $dummySessionId = 'dummy_session_test_id_12345';
        DB::table('sessions')->insert([
            'id' => $dummySessionId,
            'user_id' => $this->superAdmin->id,
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
            'payload' => 'dummy',
            'last_activity' => time(),
        ]);

        $this->assertDatabaseHas('sessions', ['id' => $dummySessionId]);

        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.profile.session.revoke', $dummySessionId));
        $response->assertRedirect(route('super_admin.profile.index', ['tab' => 'security']));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('sessions', ['id' => $dummySessionId]);
    }

    public function test_super_admin_cannot_revoke_current_active_session_directly()
    {
        $this->actingAs($this->superAdmin);
        $this->startSession();
        $currentSessionId = session()->getId();

        $response = $this->withSession([])->post(route('super_admin.profile.session.revoke', session()->getId()));
        $response->assertRedirect(route('super_admin.profile.index', ['tab' => 'security']));
        $response->assertSessionHas('error');
    }

    public function test_super_admin_can_revoke_all_other_sessions()
    {
        $session1 = 'other_session_1';
        $session2 = 'other_session_2';

        DB::table('sessions')->insert([
            ['id' => $session1, 'user_id' => $this->superAdmin->id, 'ip_address' => '10.0.0.1', 'user_agent' => 'Chrome', 'payload' => 'p1', 'last_activity' => time()],
            ['id' => $session2, 'user_id' => $this->superAdmin->id, 'ip_address' => '10.0.0.2', 'user_agent' => 'Safari', 'payload' => 'p2', 'last_activity' => time()],
        ]);

        // Wrong password
        $failedResponse = $this->actingAs($this->superAdmin)->post(route('super_admin.profile.session.revoke_others'), [
            'confirm_password' => 'wrongpass',
        ]);
        $failedResponse->assertSessionHasErrors('confirm_password');

        // Correct password
        $successResponse = $this->actingAs($this->superAdmin)->post(route('super_admin.profile.session.revoke_others'), [
            'confirm_password' => 'password123',
        ]);

        $successResponse->assertRedirect(route('super_admin.profile.index', ['tab' => 'security']));
        $successResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('sessions', ['id' => $session1]);
        $this->assertDatabaseMissing('sessions', ['id' => $session2]);
    }

    public function test_super_admin_can_update_notification_preferences()
    {
        $payload = [
            'notif_login_activity' => 1,
            'notif_account_change' => 1,
            'notif_admin_access_change' => 1,
            'notif_system_outage' => 1,
            'notif_backup_failure' => 1,
            'notif_platform_maintenance' => 1,
            'notif_platform_update' => 1,
            'channel_in_app' => 1,
            'channel_email' => 1,
        ];

        $response = $this->actingAs($this->superAdmin)
            ->post(route('super_admin.profile.notifications.update'), $payload);

        $response->assertRedirect(route('super_admin.profile.index', ['tab' => 'notifications']));
        $response->assertSessionHas('success');

        $this->superAdmin->refresh();
        $prefs = $this->superAdmin->notification_preferences;
        $this->assertTrue($prefs['notif_security_alert']); // Mandatory policy check
        $this->assertTrue($prefs['notif_backup_failure']);
        $this->assertTrue($prefs['channel_email']);
    }

    public function test_super_admin_can_update_theme_preference()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.profile.preferences.update'), [
            'theme_preference' => 'dark',
        ]);

        $response->assertRedirect(route('super_admin.profile.index', ['tab' => 'preferences']));
        $response->assertSessionHas('success');

        $this->assertEquals('dark', $this->superAdmin->fresh()->theme_preference);
    }

    public function test_super_admin_can_view_and_filter_activities()
    {
        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'user_name' => $this->superAdmin->name,
            'role' => 'super_admin',
            'event' => 'update',
            'status' => 'success',
            'action_type' => 'CONFIG_CHANGE',
            'module' => 'Pengaturan Platform',
            'description' => 'Super Admin memperbarui konfigurasi modul',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('super_admin.profile.index', [
            'tab' => 'activity',
            'act_module' => 'Pengaturan Platform',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Super Admin memperbarui konfigurasi modul');
    }
}
