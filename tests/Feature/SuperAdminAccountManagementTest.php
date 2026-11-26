<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Warga;
use App\Models\WilayahRt;
use App\Models\WilayahRw;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminRw;
    protected User $adminRt006;
    protected User $wargaUser;
    protected Warga $wargaRecord;
    protected WilayahRw $rw;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup RW 018
        $this->rw = WilayahRw::create([
            'rw_number' => '018',
            'tenant_id' => 'TENANT-RW018-MEKARJAYA',
            'nama_wilayah' => 'RW 018 Kelurahan Mekarjaya',
            'kelurahan' => 'Kelurahan Mekarjaya',
            'kecamatan' => 'Kecamatan Sukmajaya',
            'kota' => 'Kota Depok',
            'provinsi' => 'Jawa Barat',
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
        $this->rw->update(['admin_user_id' => $this->adminRw->id]);

        // 4. Setup Admin RT 006
        $this->adminRt006 = User::create([
            'name' => 'Fathir Hartono',
            'email' => 'fathir@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'admin_rt',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
        ]);

        // 5. Setup Warga (User and Warga profile)
        $this->wargaUser = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti.aminah@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
            'phone' => '081234567899',
        ]);

        $this->wargaRecord = Warga::create([
            'nama' => 'Siti Aminah',
            'user_id' => $this->wargaUser->id,
            'nik' => '3276010101019999',
            'no_kk' => '3276010101018888',
            'rw_number' => '018',
            'rt_number' => '006',
            'blok_rumah' => 'Blok A2 No. 04',
            'nomor_hp' => '081234567899',
            'status_warga' => 'tetap',
            'status_verifikasi' => 'terverifikasi',
        ]);
        $this->wargaUser->update(['warga_id' => $this->wargaRecord->id]);

        // 6. Setup initial RT records
        WilayahRt::firstOrCreate(
            ['rw_number' => '018', 'rt_number' => '006'],
            [
                'wilayah_rw_id' => $this->rw->id,
                'nama_ketua_rt' => 'Fathir Hartono',
                'admin_user_id' => $this->adminRt006->id,
                'status' => 'active',
            ]
        );
        WilayahRt::firstOrCreate(
            ['rw_number' => '018', 'rt_number' => '007'],
            [
                'wilayah_rw_id' => $this->rw->id,
                'nama_ketua_rt' => 'Gavin Gunawan',
                'status' => 'active',
            ]
        );
    }

    public function test_guest_cannot_access_accounts_management(): void
    {
        $response = $this->get(route('super_admin.accounts.index'));
        $response->assertRedirect('/login');
    }

    public function test_warga_cannot_access_accounts_management(): void
    {
        $response = $this->actingAs($this->wargaUser)->get(route('super_admin.accounts.index'));
        $response->assertStatus(403);
    }

    public function test_admin_rw_cannot_access_super_admin_accounts_management(): void
    {
        $response = $this->actingAs($this->adminRw)->get(route('super_admin.accounts.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_view_accounts_and_see_only_pengurus_accounts(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('super_admin.accounts.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Akun Pengurus &amp; Hak Akses Platform', false);
        $response->assertSee('Dirga Pradipta');
        $response->assertSee('Fathir Hartono');

        // Warga accounts MUST NOT be included in the pengurus listing
        $pengurusEmails = $response->viewData('pengurusList')->pluck('email');
        $this->assertFalse($pengurusEmails->contains('siti.aminah@gmail.com'));
        $this->assertTrue($pengurusEmails->contains('dirga@nexanest.test'));
        $this->assertTrue($pengurusEmails->contains('fathir@nexanest.test'));

        // Aggregate statistics must be visible
        $response->assertSee('Warga Terdaftar (Agregat)');
    }

    public function test_super_admin_can_create_new_admin_rt_account(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.accounts.store'), [
            'name' => 'Bambang Pamungkas',
            'email' => 'bambang.rt007@nexanest.test',
            'role' => 'admin_rt',
            'rt_number' => '007',
            'phone' => '081234567890',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Bambang Pamungkas',
            'email' => 'bambang.rt007@nexanest.test',
            'role' => 'admin',
            'rt_number' => '007',
            'rw_number' => '018',
            'status' => 'active',
        ]);

        $createdUser = User::where('email', 'bambang.rt007@nexanest.test')->first();
        $this->assertTrue(Hash::check('password123', $createdUser->password));

        // Check Wilayah RT synced
        $this->assertDatabaseHas('wilayah_rt', [
            'rw_number' => '018',
            'rt_number' => '007',
            'admin_user_id' => $createdUser->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action_type' => 'CREATE',
            'module' => 'Manajemen Akun',
        ]);
    }

    public function test_super_admin_can_create_new_admin_rw_account(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.accounts.store'), [
            'name' => 'Hendro Kartiko',
            'email' => 'hendro.rw@nexanest.test',
            'role' => 'admin_rw',
            'phone' => '081234567891',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Hendro Kartiko',
            'email' => 'hendro.rw@nexanest.test',
            'role' => 'admin',
            'rt_number' => '000',
            'rw_number' => '018',
        ]);
    }

    public function test_super_admin_can_edit_pengurus_account(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('super_admin.accounts.update', $this->adminRt006->id), [
            'name' => 'Fathir Hartono, S.Kom',
            'email' => 'fathir.updated@nexanest.test',
            'role' => 'admin_rt',
            'rt_number' => '006',
            'phone' => '085522334455',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->adminRt006->id,
            'name' => 'Fathir Hartono, S.Kom',
            'email' => 'fathir.updated@nexanest.test',
            'phone' => '085522334455',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action_type' => 'UPDATE',
            'module' => 'Manajemen Akun',
        ]);
    }

    public function test_super_admin_cannot_edit_warga_via_pengurus_module(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('super_admin.accounts.update', $this->wargaUser->id), [
            'name' => 'Siti Aminah Hack',
            'email' => 'siti.hack@gmail.com',
            'role' => 'warga',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('users', [
            'id' => $this->wargaUser->id,
            'name' => 'Siti Aminah Hack',
        ]);
    }

    public function test_super_admin_can_toggle_pengurus_status_non_destructively(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.accounts.toggle_status', $this->adminRt006->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->adminRt006->id,
            'status' => 'suspended',
        ]);

        // Toggle back
        $response2 = $this->actingAs($this->superAdmin)->post(route('super_admin.accounts.toggle_status', $this->adminRt006->id));
        $response2->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->adminRt006->id,
            'status' => 'active',
        ]);
    }

    public function test_super_admin_cannot_suspend_self_or_warga(): void
    {
        // 1. Cannot suspend self
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.accounts.toggle_status', $this->superAdmin->id));
        $response->assertSessionHas('error');

        // 2. Cannot suspend warga
        $response2 = $this->actingAs($this->superAdmin)->post(route('super_admin.accounts.toggle_status', $this->wargaUser->id));
        $response2->assertSessionHas('error');
    }

    public function test_super_admin_can_reset_pengurus_password(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.accounts.reset_password', $this->adminRt006->id), [
            'new_password' => 'secretbaru123',
            'new_password_confirmation' => 'secretbaru123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user = User::find($this->adminRt006->id);
        $this->assertTrue(Hash::check('secretbaru123', $user->password));

        $this->assertDatabaseHas('audit_logs', [
            'action_type' => 'PASSWORD_RESET',
            'module' => 'Manajemen Akun',
        ]);
    }

    public function test_super_admin_can_reassign_pengurus_to_different_rt(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.accounts.reassign'), [
            'user_id' => $this->adminRt006->id,
            'target_role' => 'admin_rt',
            'target_rt' => '007',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->adminRt006->id,
            'rt_number' => '007',
        ]);

        $this->assertDatabaseHas('wilayah_rt', [
            'rt_number' => '007',
            'admin_user_id' => $this->adminRt006->id,
        ]);
    }

    public function test_super_admin_can_revoke_pengurus_session(): void
    {
        // Insert dummy session
        DB::table('sessions')->insert([
            'id' => 'dummy_session_123',
            'user_id' => $this->adminRt006->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => 'dummy_payload',
            'last_activity' => time(),
        ]);

        $this->assertDatabaseHas('sessions', ['user_id' => $this->adminRt006->id]);

        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.accounts.revoke_session', $this->adminRt006->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('sessions', ['user_id' => $this->adminRt006->id]);
    }
}
