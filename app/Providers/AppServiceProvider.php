<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Enforce HTTPS scheme when running behind reverse proxy or non-local environment
        if (config('app.env') !== 'local' || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // 1. Kustomisasi Notifikasi Email Verifikasi Registrasi (Bahasa Indonesia & Branding NexaNest RW 018)
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email Akun — NexaNest (RW 018)')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Terima kasih telah bergabung dengan platform NexaNest — Smart & Green Community (RW 018 Kelurahan Mekarjaya).')
                ->line('Untuk mengaktifkan akun dan memastikan keamanan akses layanan kependudukan dan iuran warga, silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda:')
                ->action('Verifikasi Alamat Email', $url)
                ->line('Tautan verifikasi ini berlaku selama 60 menit. Jika Anda tidak merasa mendaftar akun di NexaNest, abaikan email ini.')
                ->salutation("Salam hangat,\nPengurus RW 018 & Tim NexaNest");
        });

        // 2. Kustomisasi Notifikasi Email Pemulihan / Reset Kata Sandi (Bahasa Indonesia & Branding NexaNest RW 018)
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Permintaan Pemulihan Kata Sandi — NexaNest (RW 018)')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Kami menerima permintaan pemulihan kata sandi untuk akun NexaNest Anda.')
                ->line('Silakan klik tombol di bawah ini untuk membuat kata sandi baru akun Anda:')
                ->action('Atur Ulang Kata Sandi', $url)
                ->line('Tautan pemulihan kata sandi ini berlaku selama 60 menit sejak email ini dikirimkan demi keamanan akun Anda.')
                ->line('Jika Anda tidak pernah meminta pengaturan ulang kata sandi, abaikan email ini dan kata sandi Anda akan tetap aman.')
                ->salutation("Salam hormat,\nPengurus RW 018 & Tim Keamanan NexaNest");
        });
    }
}
