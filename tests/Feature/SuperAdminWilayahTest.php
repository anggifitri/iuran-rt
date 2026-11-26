<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Warga;
use App\Models\WilayahRt;
use App\Models\WilayahRw;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminWilayahTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminRw;
    protected User $adminRt;
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
            'ketua_rw_nama' => 'Dirga Pradipta',
            'ketua_rw_phone' => '081234567890',
            'email_resmi' => 'sekretariat@rw018.nexanest.id',
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

        $this->rw->admin_user_id = $this->adminRw->id;
        $this->rw->save();

        // 4. Setup Admin RT
        $this->adminRt = User::create([
            'name' => 'Fathir Hartono',
            'email' => 'fathir@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'admin_rt',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
        ]);

        // 5. Setup Warga
        $this->wargaUser = User::create([
            'name' => 'Warga Wawan',
            'email' => 'warga@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
        ]);

        // 6. Setup sample RT records if migration didn't run via seeder
        $initialRts = ['006', '007', '008', '009', '010'];
        foreach ($initialRts as $rtNum) {
            WilayahRt::firstOrCreate(
                ['rw_number' => '018', 'rt_number' => $rtNum],
                [
                    'wilayah_rw_id' => $this->rw->id,
                    'nama_ketua_rt' => 'Ketua RT ' . $rtNum,
                    'phone_ketua_rt' => '081234567' . $rtNum,
                    'admin_user_id' => $rtNum === '006' ? $this->adminRt->id : null,
                    'status' => 'active',
                ]
            );
        }
    }

    public function test_guest_cannot_access_wilayah_management(): void
    {
        $response = $this->get(route('super_admin.wilayah.index'));
        $response->assertRedirect('/login');
    }

    public function test_regular_warga_cannot_access_wilayah_management(): void
    {
        $response = $this->actingAs($this->wargaUser)->get(route('super_admin.wilayah.index'));
        $response->assertStatus(403);
    }

    public function test_admin_rw_cannot_access_super_admin_wilayah_management(): void
    {
        $response = $this->actingAs($this->adminRw)->get(route('super_admin.wilayah.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_view_wilayah_overview_and_rt_list(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('super_admin.wilayah.index'));

        $response->assertStatus(200);
        $response->assertSee('Wilayah Operasional Utama: RW 018');
        $response->assertSee('TENANT-RW018-MEKARJAYA');
        $response->assertSee('RT 006');
        $response->assertSee('RT 007');
        $response->assertSee('RT 008');
        $response->assertSee('RT 009');
        $response->assertSee('RT 010');
        $response->assertSee('Dirga Pradipta');
    }

    public function test_super_admin_can_update_rw_profile(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('super_admin.wilayah.update', $this->rw->id), [
            'nama_wilayah' => 'RW 018 Mekarjaya Asri',
            'kelurahan' => 'Kelurahan Mekarjaya',
            'kecamatan' => 'Kecamatan Sukmajaya',
            'kota' => 'Kota Depok',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '16411',
            'alamat_sekretariat' => 'Balai Warga Baru No. 10',
            'ketua_rw_nama' => 'Dirga Pradipta, S.T.',
            'ketua_rw_phone' => '081299998888',
            'email_resmi' => 'info@rw018mekarjaya.id',
            'catatan' => 'Perubahan profil resmi oleh Super Admin',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wilayah_rw', [
            'id' => $this->rw->id,
            'nama_wilayah' => 'RW 018 Mekarjaya Asri',
            'alamat_sekretariat' => 'Balai Warga Baru No. 10',
            'ketua_rw_nama' => 'Dirga Pradipta, S.T.',
            'email_resmi' => 'info@rw018mekarjaya.id',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action_type' => 'UPDATE',
            'module' => 'Manajemen Wilayah',
        ]);
    }

    public function test_super_admin_can_add_new_rt_in_rw_018(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.wilayah.rt.store'), [
            'rt_number' => '011',
            'nama_ketua_rt' => 'Surya Saputra',
            'phone_ketua_rt' => '081233445566',
            'admin_user_id' => null,
            'catatan' => 'Blok K Baru',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wilayah_rt', [
            'rw_number' => '018',
            'rt_number' => '011',
            'nama_ketua_rt' => 'Surya Saputra',
            'status' => 'active',
        ]);

        // Check total_rt updated
        $this->assertEquals(6, WilayahRw::where('rw_number', '018')->first()->total_rt);

        $this->assertDatabaseHas('audit_logs', [
            'action_type' => 'CREATE',
            'module' => 'Manajemen Wilayah',
        ]);
    }

    public function test_add_rt_fails_when_rt_number_is_invalid_or_duplicate(): void
    {
        // 1. Invalid format (not 3 digits)
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.wilayah.rt.store'), [
            'rt_number' => '11',
            'nama_ketua_rt' => 'Test RT',
        ]);
        $response->assertSessionHasErrors(['rt_number']);

        // 2. Duplicate RT number in RW 018
        $response2 = $this->actingAs($this->superAdmin)->post(route('super_admin.wilayah.rt.store'), [
            'rt_number' => '006',
            'nama_ketua_rt' => 'Duplicate Ketua',
        ]);
        $response2->assertSessionHasErrors(['rt_number']);
    }

    public function test_super_admin_can_update_rt_details(): void
    {
        $rt006 = WilayahRt::where('rw_number', '018')->where('rt_number', '006')->firstOrFail();

        $response = $this->actingAs($this->superAdmin)->put(route('super_admin.wilayah.rt.update', $rt006->id), [
            'nama_ketua_rt' => 'Fathir Hartono, M.Kom',
            'phone_ketua_rt' => '085511223344',
            'admin_user_id' => $this->adminRt->id,
            'catatan' => 'Wilayah RT 006 Blok A-B Utama',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wilayah_rt', [
            'id' => $rt006->id,
            'nama_ketua_rt' => 'Fathir Hartono, M.Kom',
            'phone_ketua_rt' => '085511223344',
        ]);
    }

    public function test_super_admin_can_toggle_rt_status_non_destructively(): void
    {
        $rt007 = WilayahRt::where('rw_number', '018')->where('rt_number', '007')->firstOrFail();

        // Add dummy warga to RT 007
        $wargaRt007 = Warga::create([
            'nama' => 'Warga Sampel RT 007',
            'nik' => '3276010101010077',
            'no_kk' => '3276010101010070',
            'rw_number' => '018',
            'rt_number' => '007',
            'blok_rumah' => 'Blok C1 No. 05',
            'nomor_hp' => '081233445577',
            'status_warga' => 'tetap',
            'status_verifikasi' => 'terverifikasi',
        ]);

        // Toggle to inactive
        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.wilayah.rt.toggle_status', $rt007->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wilayah_rt', [
            'id' => $rt007->id,
            'status' => 'inactive',
        ]);

        // Verify NON-DESTRUCTIVE: citizen record remains 100% intact
        $this->assertDatabaseHas('wargas', [
            'id' => $wargaRt007->id,
            'nama' => 'Warga Sampel RT 007',
            'rt_number' => '007',
        ]);

        // Active count drops from 5 to 4
        $this->assertEquals(4, WilayahRw::where('rw_number', '018')->first()->total_rt);

        // Toggle back to active
        $response2 = $this->actingAs($this->superAdmin)->post(route('super_admin.wilayah.rt.toggle_status', $rt007->id));
        $response2->assertSessionHas('success');

        $this->assertDatabaseHas('wilayah_rt', [
            'id' => $rt007->id,
            'status' => 'active',
        ]);
        $this->assertEquals(5, WilayahRw::where('rw_number', '018')->first()->total_rt);
    }

    public function test_super_admin_can_assign_admin_rw(): void
    {
        $newAdminRwCandidate = User::create([
            'name' => 'Bambang Pamungkas',
            'email' => 'bambang@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active',
            'rw_number' => '018',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.wilayah.assign_admin'), [
            'type' => 'rw',
            'user_id' => $newAdminRwCandidate->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wilayah_rw', [
            'rw_number' => '018',
            'admin_user_id' => $newAdminRwCandidate->id,
            'ketua_rw_nama' => 'Bambang Pamungkas',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action_type' => 'ASSIGNMENT',
            'module' => 'Manajemen Wilayah',
        ]);
    }

    public function test_super_admin_can_assign_admin_rt(): void
    {
        $rt008 = WilayahRt::where('rw_number', '018')->where('rt_number', '008')->firstOrFail();

        $newAdminRtCandidate = User::create([
            'name' => 'Hendro Kartiko',
            'email' => 'hendro@nexanest.test',
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '008',
            'phone' => '081288887777',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('super_admin.wilayah.assign_admin'), [
            'type' => 'rt',
            'rt_id' => $rt008->id,
            'user_id' => $newAdminRtCandidate->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wilayah_rt', [
            'id' => $rt008->id,
            'admin_user_id' => $newAdminRtCandidate->id,
            'nama_ketua_rt' => 'Hendro Kartiko',
        ]);

        // Check user promoted to admin with RT 008
        $this->assertDatabaseHas('users', [
            'id' => $newAdminRtCandidate->id,
            'role' => 'admin',
            'rt_number' => '008',
        ]);
    }
}
