<?php

namespace Tests\Feature;

use App\Models\PlatformBackup;
use App\Models\PlatformModuleConfig;
use App\Models\PlatformNotification;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Models\WilayahRw;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SuperAdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminRw;
    protected User $wargaUser;
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
            'alamat_sekretariat' => 'Balai Warga RW 018, Jl. Boulevard Utama No. 01',
            'status' => 'active',
            'total_rt' => 5,
        ]);

        // 2. Setup Super Admin
        $this->superAdmin = User::create([
            'name' => 'Super Admin HQ',
            'email' => 'superadmin@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'status' => 'active',
            'rw_number' => '018',
        ]);

        // 3. Setup Admin RW
        $this->adminRw = User::create([
            'name' => 'Dirga Pradipta',
            'email' => 'dirga@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'admin_rw',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '000',
        ]);

        // 4. Setup Warga
        $this->wargaUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
        ]);

        // Seed some platform settings
        PlatformSetting::set('platform_name', 'NexaNest Smart Community Platform');
        PlatformSetting::set('super_admin_contact', 'anggifitri0911@gmail.com');
        PlatformSetting::set('default_rw', '018');
        PlatformSetting::set('maintenance_mode', '0');

        // Seed sample platform module config
        PlatformModuleConfig::create([
            'module_key' => 'bank_sampah',
            'module_name' => 'Bank Sampah Digital',
            'description' => 'Pengelolaan tabungan sampah anorganik warga terpadu',
            'icon' => 'fa-recycle',
            'is_enabled_globally' => true,
            'disabled_rw_numbers' => [],
        ]);
    }

    public function test_super_admin_can_access_settings_hub_and_all_tabs()
    {
        $tabs = ['general', 'modules', 'notifications', 'backup', 'security'];

        foreach ($tabs as $tab) {
            $response = $this->actingAs($this->superAdmin)->get(route('super_admin.settings.index', ['tab' => $tab]));
            $response->assertStatus(200);
            $response->assertSee('Pengaturan &amp; Konfigurasi Platform', false);
        }
    }

    public function test_non_super_admin_is_forbidden_from_settings()
    {
        $responseAdminRw = $this->actingAs($this->adminRw)->get(route('super_admin.settings.index'));
        $responseAdminRw->assertStatus(403);

        $responseWarga = $this->actingAs($this->wargaUser)->get(route('super_admin.settings.index'));
        $responseWarga->assertStatus(403);
    }

    public function test_super_admin_can_update_general_settings()
    {
        $payload = [
            'platform_name' => 'NexaNest NextGen',
            'platform_tagline' => 'Next Generation Neighborhood',
            'platform_description' => 'Platform rukun warga generasi baru.',
            'super_admin_contact' => 'hq@nexanest.test',
            'super_admin_phone' => '+62 811-9988-7766',
            'app_timezone' => 'Asia/Jakarta',
            'date_format' => 'd M Y',
            'time_format' => 'H:i',
            'default_rw' => '018',
            'maintenance_mode' => '1',
            'maintenance_rw_018' => '0',
            'maintenance_reason' => 'Pembaruan keamanan database',
            'maintenance_estimated_end' => '1 Jam',
            'maintenance_message' => 'Sistem sedang ditingkatkan.',
        ];

        $response = $this->actingAs($this->superAdmin)
            ->post(route('super_admin.settings.general.update'), $payload);

        $response->assertRedirect(route('super_admin.settings.index', ['tab' => 'general']));
        $this->assertEquals('NexaNest NextGen', PlatformSetting::get('platform_name'));
        $this->assertEquals('hq@nexanest.test', PlatformSetting::get('super_admin_contact'));
        $this->assertEquals('1', PlatformSetting::get('maintenance_mode'));
    }

    public function test_super_admin_can_toggle_maintenance_mode()
    {
        // Toggle Global Maintenance
        $responseGlobal = $this->actingAs($this->superAdmin)
            ->post(route('super_admin.settings.maintenance.toggle'), ['scope' => 'global']);

        $responseGlobal->assertRedirect(route('super_admin.settings.index', ['tab' => 'general']));
        $this->assertEquals('1', PlatformSetting::get('maintenance_mode'));

        // Toggle again to turn off
        $this->actingAs($this->superAdmin)
            ->post(route('super_admin.settings.maintenance.toggle'), ['scope' => 'global']);
        $this->assertEquals('0', PlatformSetting::get('maintenance_mode'));

        // Toggle RW 018 Maintenance
        $this->actingAs($this->superAdmin)
            ->post(route('super_admin.settings.maintenance.toggle'), ['scope' => 'rw_018']);
        $this->assertEquals('1', PlatformSetting::get('maintenance_rw_018'));
    }

    public function test_super_admin_can_toggle_module_globally_and_for_rw018()
    {
        $module = PlatformModuleConfig::where('module_key', 'bank_sampah')->first();
        $this->assertTrue($module->is_enabled_globally);

        // Toggle global (disable)
        $response = $this->actingAs($this->superAdmin)
            ->post(route('super_admin.settings.modules.toggle_global', $module->id));

        $response->assertRedirect(route('super_admin.settings.index', ['tab' => 'modules']));
        $module->refresh();
        $this->assertFalse($module->is_enabled_globally);

        // Toggle RW 018 access
        $this->actingAs($this->superAdmin)
            ->post(route('super_admin.settings.modules.toggle_rw', $module->id));

        $module->refresh();
        $this->assertContains('018', $module->disabled_rw_numbers);

        // Toggle RW 018 back to enabled
        $this->actingAs($this->superAdmin)
            ->post(route('super_admin.settings.modules.toggle_rw', $module->id));

        $module->refresh();
        $this->assertNotContains('018', $module->disabled_rw_numbers);
    }

    public function test_super_admin_can_manage_platform_notifications()
    {
        // Store
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.settings.notifications.store'), [
            'title' => 'Pemberitahuan Pembaruan Sistem v2.4',
            'message' => 'Aplikasi telah diperbarui dengan patch keamanan terbaru.',
            'type' => 'info',
            'target_scope' => 'all',
            'is_banner' => 1,
            'expires_at' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('super_admin.settings.index', ['tab' => 'notifications']));
        $this->assertDatabaseHas('platform_notifications', [
            'title' => 'Pemberitahuan Pembaruan Sistem v2.4',
            'type' => 'info',
            'target_scope' => 'all',
        ]);

        $notif = PlatformNotification::where('title', 'Pemberitahuan Pembaruan Sistem v2.4')->first();

        // Update
        $this->actingAs($this->superAdmin)->put(route('super_admin.settings.notifications.update', $notif->id), [
            'title' => 'Pemberitahuan Pembaruan Sistem v2.4 (Revisi)',
            'message' => 'Pesan telah diperbarui.',
            'type' => 'warning',
            'target_scope' => 'rw',
            'target_rw' => '018',
            'is_banner' => 0,
        ]);

        $notif->refresh();
        $this->assertEquals('Pemberitahuan Pembaruan Sistem v2.4 (Revisi)', $notif->title);
        $this->assertEquals('warning', $notif->type);

        // Destroy
        $this->actingAs($this->superAdmin)->delete(route('super_admin.settings.notifications.destroy', $notif->id));
        $this->assertDatabaseMissing('platform_notifications', ['id' => $notif->id]);
    }

    public function test_super_admin_can_create_backup_and_download()
    {
        // Ensure test sqlite database exists
        $dbPath = database_path('database.sqlite');
        if (!file_exists($dbPath)) {
            File::put($dbPath, '');
        }

        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.settings.backup.create'));
        $response->assertRedirect(route('super_admin.settings.index', ['tab' => 'backup']));

        $latestBackup = PlatformBackup::latest()->first();
        $this->assertNotNull($latestBackup);
        $this->assertStringContainsString('nexanest_backup_', $latestBackup->filename);

        // Download
        $downloadResponse = $this->actingAs($this->superAdmin)->get(route('super_admin.settings.backup.download', $latestBackup->id));
        $downloadResponse->assertOk();
    }

    public function test_super_admin_can_update_backup_schedule()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.settings.backup.schedule'), [
            'backup_schedule_frequency' => 'daily',
            'backup_schedule_time' => '03:30',
            'backup_retention_days' => 45,
        ]);

        $response->assertRedirect(route('super_admin.settings.index', ['tab' => 'backup']));
        $this->assertEquals('daily', PlatformSetting::get('backup_schedule_frequency'));
        $this->assertEquals('03:30', PlatformSetting::get('backup_schedule_time'));
        $this->assertEquals('45', PlatformSetting::get('backup_retention_days'));
    }

    public function test_super_admin_can_restore_backup_with_safety_snapshot()
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $dbPath = database_path('database.sqlite');
        $originalDbContent = file_exists($dbPath) ? File::get($dbPath) : null;

        $dummyBackupFile = 'nexanest_test_backup.sqlite';
        // Copy actual valid sqlite database so restoring doesn't corrupt database file
        if (file_exists($dbPath)) {
            File::copy($dbPath, $backupDir . '/' . $dummyBackupFile);
        } else {
            File::put($backupDir . '/' . $dummyBackupFile, '');
        }

        $backup = PlatformBackup::create([
            'filename' => $dummyBackupFile,
            'filesize' => '1.00 MB',
            'type' => 'database',
            'status' => 'success',
            'notes' => 'Test backup for restore',
            'created_by' => $this->superAdmin->id,
        ]);

        // Attempt with wrong password
        $failedResponse = $this->actingAs($this->superAdmin)->post(route('super_admin.settings.backup.restore', $backup->id), [
            'confirm_restore' => 'PULIHKAN',
            'password' => 'wrongpassword',
        ]);
        $failedResponse->assertRedirect(route('super_admin.settings.index', ['tab' => 'backup']));
        $failedResponse->assertSessionHas('error');

        // Attempt with correct password
        $successResponse = $this->actingAs($this->superAdmin)->post(route('super_admin.settings.backup.restore', $backup->id), [
            'confirm_restore' => 'PULIHKAN',
            'password' => 'password123',
        ]);
        $successResponse->assertRedirect(route('super_admin.settings.index', ['tab' => 'backup']));
        $successResponse->assertSessionHas('success');

        // Verify safety snapshot was created before restore
        $safetySnapshot = PlatformBackup::where('type', 'safety_snapshot')->first();
        $this->assertNotNull($safetySnapshot);

        // Always restore original database content to guarantee zero corruption of live DB
        if ($originalDbContent !== null) {
            File::put($dbPath, $originalDbContent);
        }

        // Cleanup dummy file
        if (file_exists($backupDir . '/' . $dummyBackupFile)) {
            File::delete($backupDir . '/' . $dummyBackupFile);
        }
    }

    public function test_super_admin_can_update_security_settings()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.settings.security.update'), [
            'sensitive_data_auth_timeout_minutes' => 30,
            'max_login_attempts' => 4,
            'lockout_duration_minutes' => 20,
            'allow_self_rw_registration' => 0,
            'max_rt_per_rw' => 30,
        ]);

        $response->assertRedirect(route('super_admin.settings.index', ['tab' => 'security']));
        $this->assertEquals('30', PlatformSetting::get('sensitive_data_auth_timeout_minutes'));
        $this->assertEquals('4', PlatformSetting::get('max_login_attempts'));
        $this->assertEquals('20', PlatformSetting::get('lockout_duration_minutes'));
        $this->assertEquals('0', PlatformSetting::get('allow_self_rw_registration'));
        $this->assertEquals('30', PlatformSetting::get('max_rt_per_rw'));
    }
}
