<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warga;
use App\Models\WilayahRw;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminRoleSwitcherTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminRw;
    protected User $adminRt006;
    protected User $wargaUser;
    protected Warga $wargaRecord;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Wilayah RW 018
        WilayahRw::create([
            'rw_number' => '018',
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
            'email' => 'adminrw@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'admin_rw',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '000',
        ]);

        // 4. Setup Admin RT 006
        $this->adminRt006 = User::create([
            'name' => 'Fathir Hartono',
            'email' => 'adminrt6@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'admin_rt',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
        ]);

        // 5. Setup Citizen & Citizen User in RT 006
        $this->wargaUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'warga1@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
        ]);

        $this->wargaRecord = Warga::create([
            'nama' => 'Budi Santoso',
            'user_id' => $this->wargaUser->id,
            'no_kk' => '3276010101010001',
            'nik' => '3276010101010002',
            'rt_number' => '006',
            'rw_number' => '018',
            'is_kk' => true,
            'blok_rumah' => 'Blok B3 No. 12',
            'nomor_hp' => '081234567891',
        ]);

        $this->wargaUser->update(['warga_id' => $this->wargaRecord->id]);
    }

    /**
     * Test 1: Super Admin can query warga-by-rt API
     */
    public function test_super_admin_can_query_warga_by_rt_api()
    {
        $response = $this->actingAs($this->superAdmin)->getJson('/super-admin/role-switch/warga-by-rt/006');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'rt' => '006',
            'count' => 1,
        ]);
        $response->assertJsonFragment([
            'nama' => 'Budi Santoso',
            'warga_id' => $this->wargaRecord->id,
        ]);
    }

    /**
     * Test 2: Non-Super Admin cannot query warga-by-rt API
     */
    public function test_non_super_admin_cannot_query_warga_by_rt()
    {
        $response = $this->actingAs($this->adminRw)->getJson('/super-admin/role-switch/warga-by-rt/006');
        $this->assertTrue(in_array($response->status(), [403, 302]));

        $responseWarga = $this->actingAs($this->wargaUser)->getJson('/super-admin/role-switch/warga-by-rt/006');
        $this->assertTrue(in_array($responseWarga->status(), [403, 302]));
    }

    /**
     * Test 3: Switching to Warga without RT fails validation
     */
    public function test_switching_to_warga_without_rt_fails()
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/role-switch', [
            'target_role' => 'warga',
            'target_rw' => '018',
            'target_rt' => '',
            'mode' => 'preview',
        ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test 4: Switching to Warga without selecting a citizen fails validation
     */
    public function test_switching_to_warga_without_warga_fails()
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/role-switch', [
            'target_role' => 'warga',
            'target_rw' => '018',
            'target_rt' => '006',
            'target_warga_id' => '',
            'mode' => 'preview',
        ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test 5: Switching to Warga with valid RT & Warga succeeds and resolves citizen data
     */
    public function test_switching_to_warga_with_valid_warga_succeeds()
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/role-switch', [
            'target_role' => 'warga',
            'target_rw' => '018',
            'target_rt' => '006',
            'target_warga_id' => $this->wargaRecord->id,
            'mode' => 'operational',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        // Check session context
        $sessionCtx = session('role_switch');
        $this->assertNotNull($sessionCtx);
        $this->assertEquals('warga', $sessionCtx['target_role']);
        $this->assertEquals('006', $sessionCtx['target_rt']);
        $this->assertEquals($this->wargaRecord->id, $sessionCtx['target_warga_id']);
        $this->assertEquals('Budi Santoso', $sessionCtx['target_user_name']);

        // Check resolveWarga on Super Admin user
        $resolved = $this->superAdmin->resolveWarga();
        $this->assertNotNull($resolved);
        $this->assertEquals($this->wargaRecord->id, $resolved->id);
        $this->assertEquals('Budi Santoso', $resolved->nama);
        $this->assertEquals(['3276010101010001'], array_unique(Warga::whereIn('id', $this->superAdmin->familyWargaIds())->pluck('no_kk')->toArray()));
    }

    /**
     * Test 6: Switching to Admin RT requires valid RT
     */
    public function test_switching_to_admin_rt_succeeds()
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/role-switch', [
            'target_role' => 'admin_rt',
            'target_rw' => '018',
            'target_rt' => '006',
            'mode' => 'preview',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('admin_rt', session('role_switch.target_role'));
        $this->assertEquals('006', session('role_switch.target_rt'));
    }

    /**
     * Test 7: Switching to Admin RW does not require selecting RT
     */
    public function test_switching_to_admin_rw_succeeds()
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/role-switch', [
            'target_role' => 'admin_rw',
            'target_rw' => '018',
            'mode' => 'preview',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('admin_rw', session('role_switch.target_role'));
        $this->assertEquals('000', session('role_switch.target_rt'));
    }

    /**
     * Test 8: Exit role switcher clears session and redirects to super-admin dashboard
     */
    public function test_exit_role_switcher_succeeds()
    {
        // First switch
        session(['role_switch' => [
            'is_switched' => true,
            'target_role' => 'warga',
            'target_rw' => '018',
            'target_rt' => '006',
        ]]);

        $response = $this->actingAs($this->superAdmin)->post('/super-admin/role-switch/exit');
        $response->assertRedirect(route('super_admin.dashboard'));
        $this->assertNull(session('role_switch'));
    }

    /**
     * Test 9: Avatar and initials synchronization during role switcher
     */
    public function test_avatar_and_initials_sync_during_role_switcher()
    {
        // 9.1 Unit test inisial huruf
        $this->assertEquals('AP', User::extractInitials('Adrian Pradana'));
        $this->assertEquals('DP', User::extractInitials('Dirga Pradipta'));
        $this->assertEquals('FH', User::extractInitials('Fathir Hartono'));
        $this->assertEquals('SU', User::extractInitials('Bpk. Sutrisno'));

        // 9.2 Switch role to warga (wargaUser / wargaRecord)
        $this->actingAs($this->superAdmin)->post('/super-admin/role-switch', [
            'target_role' => 'warga',
            'target_rw' => '018',
            'target_rt' => '006',
            'target_warga_id' => $this->wargaRecord->id,
            'mode' => 'preview',
        ]);

        $this->assertTrue($this->superAdmin->isRoleSwitched());
        $photoUrl = $this->superAdmin->profile_photo_url;
        // Warga without uploaded photo must have initials avatar SVG data URI (not Super Admin's photo)
        $this->assertStringContainsString('data:image/svg+xml', $photoUrl);

        // 9.3 Dashboard renders target avatar and target display name
        $dash = $this->actingAs($this->superAdmin)->get(route('dashboard'));
        $dash->assertStatus(200);
        $dash->assertSee($this->wargaRecord->nama);
        $dash->assertSee('nx-profile-avatar');
    }
}
