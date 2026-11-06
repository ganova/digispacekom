<?php

namespace App\Support;

use App\Mail\BookingReceived;
use App\Models\Booking;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

/** Pemberitahuan email untuk permintaan masuk; pengaturannya dikelola dari CMS (Notifikasi email). */
class Notify
{
    /** Alamat penerima yang valid. */
    public static function recipients(): array
    {
        return array_values(array_filter((array) config('site.notify_emails'), fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
    }

    public static function ready(): bool
    {
        return self::recipients() !== [] && config('site.smtp_host');
    }

    /** Pecah isian "a@x.com, b@y.com" (koma, titik koma, atau baris baru) menjadi daftar. */
    public static function parse(?string $text): array
    {
        return array_values(array_unique(array_filter(preg_split('/[\s,;]+/', trim((string) $text)))));
    }

    /** Terapkan server SMTP dari CMS; tanpa server SMTP, pengaturan .env dipakai seperti biasa. */
    public static function configureMail(): void
    {
        $host = config('site.smtp_host');

        if (! $host) {
            return;
        }

        try {
            $pass = config('site.smtp_pass') ? Crypt::decryptString(config('site.smtp_pass')) : null;
        } catch (\Throwable) {
            $pass = null; // kunci aplikasi berganti: password perlu diisi ulang
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => (int) (config('site.smtp_port') ?: 587),
            'mail.mailers.smtp.username' => config('site.smtp_user') ?: null,
            'mail.mailers.smtp.password' => $pass,
            // 465 = SSL langsung (smtps); 587 = STARTTLS otomatis
            'mail.mailers.smtp.scheme' => config('site.smtp_secure') === 'ssl' ? 'smtps' : 'smtp',
            'mail.mailers.smtp.timeout' => 10,
            'mail.from.address' => config('site.mail_from') ?: (config('site.smtp_user') ?: config('mail.from.address')),
            'mail.from.name' => config('site.name'),
        ]);
    }

    /** Kirim setelah respons ke pengunjung terkirim; gagal kirim tidak boleh mengganggu form. */
    public static function booking(Booking $booking): void
    {
        if (! self::ready()) {
            return;
        }

        app()->terminating(function () use ($booking) {
            try {
                Mail::to(self::recipients())->send(new BookingReceived($booking));
            } catch (\Throwable $e) {
                report($e);
            }
        });
    }
}
