<?php

namespace Tests\Feature;

use App\Mail\BookingReceived;
use App\Models\Setting;
use App\Models\User;
use App\Support\Notify;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailNotifyTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function settings(array $extra = []): array
    {
        return $extra + [
            'notify_emails' => "owner@contoh.test, staf@contoh.test\nowner@contoh.test",
            'smtp_host' => 'smtp.contoh.test', 'smtp_port' => 587, 'smtp_secure' => 'tls',
            'smtp_user' => 'kirim@contoh.test', 'smtp_pass' => 'rahasia-app-password',
        ];
    }

    public function test_only_admin_can_open_mail_settings(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staf']));
        $this->get('/admin/notifikasi-email')->assertForbidden();
        $this->put('/admin/notifikasi-email', $this->settings())->assertForbidden();

        $this->actingAs(User::factory()->create());
        $this->get('/admin/notifikasi-email')->assertOk()->assertSee('Notifikasi email')->assertDontSee('<x-admin', false);
    }

    public function test_settings_are_saved_with_encrypted_password_that_is_never_shown(): void
    {
        $this->actingAs(User::factory()->create());

        $this->put('/admin/notifikasi-email', $this->settings())->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame(['owner@contoh.test', 'staf@contoh.test'], config('site.notify_emails'));
        $stored = Setting::where('key', 'smtp_pass')->value('value');
        $this->assertNotSame('rahasia-app-password', $stored);
        $this->assertSame('rahasia-app-password', Crypt::decryptString($stored));

        $this->get('/admin/notifikasi-email')->assertOk()->assertDontSee('rahasia-app-password')->assertSee('smtp.contoh.test');

        // password dikosongkan = tetap yang lama; centang hapus = dihapus
        $this->put('/admin/notifikasi-email', $this->settings(['smtp_pass' => '']))->assertRedirect();
        $this->assertSame('rahasia-app-password', Crypt::decryptString(Setting::where('key', 'smtp_pass')->value('value')));

        $this->put('/admin/notifikasi-email', $this->settings(['smtp_pass' => '', 'smtp_pass_clear' => '1']))->assertRedirect();
        $this->assertDatabaseMissing('settings', ['key' => 'smtp_pass']);

        $this->post('/admin/notifikasi-email/hapus')->assertRedirect();
        $this->assertDatabaseMissing('settings', ['key' => 'smtp_host']);
        $this->assertSame([], config('site.notify_emails'));
    }

    public function test_invalid_input_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());

        $this->put('/admin/notifikasi-email', $this->settings(['notify_emails' => 'bukan-email']))->assertSessionHasErrors('notify_list.0');
        $this->put('/admin/notifikasi-email', $this->settings(['smtp_host' => 'smtp.x.test/<script>']))->assertSessionHasErrors('smtp_host');
        $this->put('/admin/notifikasi-email', $this->settings(['smtp_port' => 99999]))->assertSessionHasErrors('smtp_port');
        $this->put('/admin/notifikasi-email', ['notify_emails' => 'a@contoh.test', 'smtp_secure' => 'tls'])->assertSessionHasErrors('smtp_host');
        $this->put('/admin/notifikasi-email', $this->settings(['notify_emails' => 'a@x.test,b@x.test,c@x.test,d@x.test,e@x.test,f@x.test']))
            ->assertSessionHasErrors('notify_list');
    }

    public function test_new_booking_sends_email_to_recipients_only_when_configured(): void
    {
        Mail::fake();
        $form = ['type' => 'booking', 'device' => 'Laptop', 'complaint' => 'Mati total', 'location' => 'Bekasi'];

        $this->post('/booking', $form)->assertRedirect();
        Mail::assertNothingSent();

        $this->actingAs(User::factory()->create());
        $this->put('/admin/notifikasi-email', $this->settings())->assertRedirect();

        $this->post('/booking', $form)->assertRedirect();
        Mail::assertSent(BookingReceived::class, function (BookingReceived $m) {
            return $m->hasTo('owner@contoh.test') && $m->hasTo('staf@contoh.test')
                && str_contains($m->envelope()->subject, 'Booking teknisi baru')
                && str_contains($m->render(), 'Mati total');
        });
    }

    public function test_mail_failure_never_breaks_the_public_form(): void
    {
        config(['site.notify_emails' => ['owner@contoh.test'], 'site.smtp_host' => '127.0.0.1', 'site.smtp_port' => 9, 'site.smtp_pass' => null]);
        Notify::configureMail();

        $this->post('/booking', ['type' => 'booking', 'device' => 'PC', 'complaint' => 'Lambat', 'location' => 'Depok'])->assertRedirect();
        $this->assertDatabaseHas('bookings', ['complaint' => 'Lambat']);
    }

    public function test_test_email_requires_configuration(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/admin/notifikasi-email/tes')->assertSessionHasErrors('test');
    }
}
