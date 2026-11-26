<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warga;
use App\Models\WilayahRw;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationAndResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected WilayahRw $rw;
    protected User $existingWarga;
    protected Warga $wargaRecord;

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
            'kode_pos' => '16411',
            'alamat_sekretariat' => 'Balai Warga RW 018',
            'status' => 'active',
            'total_rt' => 5,
        ]);

        // 2. Setup Existing Warga (Sudah terverifikasi)
        $this->existingWarga = User::create([
            'name' => 'Warga Eksisting',
            'email' => 'warga.eksisting@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '006',
            'email_verified_at' => now(),
        ]);

        $this->wargaRecord = Warga::create([
            'user_id' => $this->existingWarga->id,
            'nama' => 'Warga Eksisting',
            'nik' => '3276010000000010',
            'no_kk' => '3276010000000099',
            'blok_rumah' => 'A1/01',
            'nomor_rumah' => '01',
            'alamat' => 'Jl. Anggrek No. 01',
            'rt_number' => '006',
            'rw_number' => '018',
            'jenis_kelamin' => 'L',
            'status_verifikasi' => 'verified',
        ]);
    }

    /**
     * 1. Akun eksisting dapat langsung login tanpa terhalang layar verifikasi
     */
    public function test_existing_users_can_login_directly_without_verification_screen(): void
    {
        $response = $this->post('/login', [
            'email' => 'warga.eksisting@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->existingWarga);
    }

    /**
     * 2. Pendaftaran warga baru mengirimkan notifikasi email verifikasi dan mengarahkan ke /email/verify
     */
    public function test_new_warga_registration_triggers_verification_and_redirects_to_notice(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Bambang Sudirman',
            'email' => 'bambang.sudirman@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'blok_rumah' => 'B2/05',
            'phone' => '081234567890',
            'no_kk' => '3276019999990001',
            'nik' => '3276019999990002',
            'gender' => 'L',
            'tanggal_lahir' => '1992-04-12',
            'rt_number' => '007',
            'rw_number' => '018',
            'is_kk' => '1',
            'address' => 'Jl. Bougenville No. 05',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('success');

        $newUser = User::where('email', 'bambang.sudirman@gmail.com')->first();
        $this->assertNotNull($newUser);
        $this->assertNull($newUser->email_verified_at);

        // Pastikan notifikasi email verifikasi terkirim ke user baru
        Notification::assertSentTo($newUser, VerifyEmail::class);
    }

    /**
     * 3. Warga yang belum verifikasi diarahkan ke /email/verify saat mengakses /dashboard
     */
    public function test_unverified_warga_is_redirected_to_notice_when_accessing_dashboard(): void
    {
        $unverifiedUser = User::create([
            'name' => 'Warga Belum Verifikasi',
            'email' => 'unverified@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '008',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($unverifiedUser)->get('/dashboard');
        $response->assertRedirect(route('verification.notice'));
    }

    /**
     * 4. Tautan verifikasi email yang valid berhasil memverifikasi akun dan masuk ke dashboard
     */
    public function test_valid_verification_link_verifies_email_and_redirects_to_dashboard(): void
    {
        $unverifiedUser = User::create([
            'name' => 'Warga Verifikasi Link',
            'email' => 'verif.link@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '008',
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $unverifiedUser->id,
                'hash' => sha1($unverifiedUser->getEmailForVerification()),
            ]
        );

        $response = $this->actingAs($unverifiedUser)->get($verificationUrl);
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $unverifiedUser->refresh();
        $this->assertNotNull($unverifiedUser->email_verified_at);
        $this->assertTrue($unverifiedUser->hasVerifiedEmail());
    }

    /**
     * 5. Tombol kirim ulang email verifikasi mengirimkan notifikasi baru
     */
    public function test_resend_verification_email_sends_notification(): void
    {
        Notification::fake();

        $unverifiedUser = User::create([
            'name' => 'Warga Resend',
            'email' => 'warga.resend@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status' => 'active',
            'rw_number' => '018',
            'rt_number' => '009',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($unverifiedUser)->post(route('verification.send'));
        $response->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($unverifiedUser, VerifyEmail::class);
    }

    /**
     * 6. Form lupa password mengirimkan email berisi tautan reset kata sandi
     */
    public function test_forgot_password_sends_reset_link_email(): void
    {
        Notification::fake();

        $response = $this->post(route('password.email'), [
            'email' => $this->existingWarga->email,
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($this->existingWarga, ResetPassword::class);
    }

    /**
     * 7. Reset password dengan token valid memperbarui kata sandi dan dapat digunakan login
     */
    public function test_reset_password_with_valid_token_updates_password(): void
    {
        $token = Password::broker()->createToken($this->existingWarga);

        // 7.1 Halaman reset password dapat diakses dengan token
        $viewResponse = $this->get(route('password.reset', ['token' => $token, 'email' => $this->existingWarga->email]));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Atur Ulang Kata Sandi');

        // 7.2 Submit password baru
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $this->existingWarga->email,
            'password' => 'kataSandiBaru2026',
            'password_confirmation' => 'kataSandiBaru2026',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // 7.3 Verifikasi login dengan kata sandi baru
        $loginResponse = $this->post('/login', [
            'email' => $this->existingWarga->email,
            'password' => 'kataSandiBaru2026',
        ]);

        $loginResponse->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->existingWarga);
    }
}
