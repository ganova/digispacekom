<?php

namespace Tests\Feature;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_company_profile_pages_render(): void
    {
        foreach (['/', '/layanan', '/layanan/service-printer', '/tentang', '/kontak', '/artikel', '/artikel/wifi-kantor-lambat'] as $url) {
            $this->get($url)->assertOk()->assertSee('DIGITAL SPACE');
        }

        $this->get('/layanan/tidak-ada')->assertNotFound();
    }

    public function test_landing_pages_render_and_prefill(): void
    {
        $this->get('/servis-panggilan?perangkat=Printer&keluhan=Paper+jam')
            ->assertOk()
            ->assertSee('Booking teknisi')
            ->assertSee('value="Printer" checked', false)
            ->assertSee('Paper jam');

        $this->get('/it-support-kantor')->assertOk()->assertSee('Minta jadwal survei');
    }

    public function test_price_search_and_category_filter(): void
    {
        $this->get('/cek-harga?q=mati')
            ->assertOk()
            ->assertSee('Laptop mati total')
            ->assertSee('Rp150rb–450rb')
            ->assertDontSee('Pasang infus tinta');

        $this->get('/cek-harga?kategori=printer')
            ->assertOk()
            ->assertSee('Pasang infus tinta')
            ->assertDontSee('Laptop mati total');
    }

    public function test_status_lookup(): void
    {
        $this->get('/cek-status?tiket=dsk-1028')
            ->assertOk()
            ->assertSee('Menunggu sparepart')
            ->assertSee('Budi S****')
            ->assertDontSee('Santoso');

        $this->get('/cek-status?tiket=DSK-9999')->assertOk()->assertSee('tidak ditemukan');
    }

    public function test_booking_is_stored_and_redirects_to_whatsapp(): void
    {
        $response = $this->post('/booking', [
            'source' => 'servis-panggilan',
            'device' => 'Laptop',
            'complaint' => 'Mati total',
            'location' => 'Bekasi Barat',
        ]);

        $response->assertRedirectContains('https://wa.me/6281385028980?text=');
        $this->assertDatabaseHas('bookings', ['type' => 'booking', 'device' => 'Laptop', 'location' => 'Bekasi Barat']);
    }

    public function test_quote_requires_name_and_honeypot_blocks_bots(): void
    {
        $this->post('/booking', ['type' => 'penawaran', 'complaint' => '30 PC'])->assertSessionHasErrors('name');

        $this->post('/booking', ['complaint' => 'spam', 'location' => 'x', 'website' => 'http://spam'])->assertRedirect();
        $this->assertSame(0, Booking::count());
    }

    public function test_sitemap_lists_pages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('services.show', 'server-jaringan'))
            ->assertSee(route('landing', 'servis-panggilan'));
    }
}
