<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Booking;
use App\Models\PriceItem;
use App\Models\ServiceTicket;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_guests_are_sent_to_login_and_pages_are_not_indexed(): void
    {
        foreach (['/admin', '/admin/permintaan', '/admin/harga', '/admin/profil', '/admin/pengguna'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }

        $this->get('/admin/masuk')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_login_logout_and_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'rahasia-panjang-1']);

        $this->post('/admin/masuk', ['email' => $user->email, 'password' => 'salah'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/admin/masuk', ['email' => $user->email, 'password' => 'rahasia-panjang-1'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/admin/keluar')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_every_admin_page_renders(): void
    {
        $this->actingAs($this->admin());

        $booking = Booking::create(['name' => 'Siti', 'complaint' => 'Printer macet', 'location' => 'Bekasi']);
        $ticket = ServiceTicket::first();
        $price = PriceItem::first();
        $article = Article::first();

        $urls = [
            '/admin', '/admin/permintaan', "/admin/permintaan/{$booking->id}", '/admin/harga', '/admin/harga/create',
            "/admin/harga/{$price->id}/edit", '/admin/artikel', '/admin/artikel/create', "/admin/artikel/{$article->slug}/edit",
            '/admin/tiket', '/admin/tiket/create', "/admin/tiket/{$ticket->id}/edit", '/admin/layanan', '/admin/layanan/baru',
            '/admin/layanan/service-printer', '/admin/tab-beranda', '/admin/landing', '/admin/landing/servis-panggilan',
            '/admin/profil', '/admin/daftar', '/admin/akun', '/admin/pengguna', '/admin/pengguna/baru', '/admin/klien',
        ];

        foreach ($urls as $url) {
            // tag komponen yang keluar mentah berarti atribut Blade-nya rusak (mis. tanda kutip bersarang)
            $this->get($url)->assertOk()->assertDontSee('<x-admin', false);
        }

        $this->get('/admin/layanan/tidak-ada')->assertNotFound();
        $this->get('/admin/landing/tidak-ada')->assertNotFound();
    }

    public function test_booking_status_update_and_csv_export(): void
    {
        $this->actingAs($this->admin());
        $booking = Booking::create(['name' => 'Budi', 'complaint' => 'Laptop mati', 'location' => 'Depok']);

        $this->put("/admin/permintaan/{$booking->id}", ['status' => 'dihubungi', 'admin_note' => 'Sudah ditelepon'])
            ->assertSessionHasNoErrors();

        $this->assertSame('dihubungi', $booking->fresh()->status);

        $this->get('/admin/permintaan/unduh?status=dihubungi')
            ->assertOk()
            ->assertDownload();

        $this->put("/admin/permintaan/{$booking->id}", ['status' => 'ngawur'])->assertSessionHasErrors('status');
    }

    public function test_price_item_crud_changes_public_page(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/harga', [
            'category' => 'printer', 'name' => 'Cuci head printer Epson', 'price_min' => 85000, 'price_max' => 120000,
        ])->assertRedirect(route('admin.prices.index'));

        $this->get('/cek-harga?q=epson')->assertOk()->assertSee('Cuci head printer Epson')->assertSee('Rp85rb–120rb');

        $item = PriceItem::where('name', 'Cuci head printer Epson')->first();
        $this->put("/admin/harga/{$item->id}", ['category' => 'printer', 'name' => 'Cuci head Epson', 'price_min' => 90000])
            ->assertSessionHasNoErrors();
        $this->get('/cek-harga?q=epson')->assertSee('Rp90rb');

        $this->post('/admin/harga', ['category' => 'printer', 'name' => 'x', 'price_min' => 5000, 'price_max' => 1000])
            ->assertSessionHasErrors('price_max');

        $this->delete("/admin/harga/{$item->id}");
        $this->assertDatabaseMissing('price_items', ['id' => $item->id]);
    }

    public function test_article_publish_draft_and_unique_slug(): void
    {
        $this->actingAs($this->admin());

        $payload = ['title' => 'Tips Baru Servis', 'excerpt' => 'Ringkas.', 'body' => '<p>Isi.</p>', 'publish' => 'now'];

        $this->post('/admin/artikel', $payload)->assertRedirect(route('admin.articles.index'));
        $this->post('/admin/artikel', ['publish' => 'draft'] + $payload)->assertRedirect(route('admin.articles.index'));

        $this->assertSame(['tips-baru-servis', 'tips-baru-servis-2'], Article::where('title', 'Tips Baru Servis')->orderBy('id')->pluck('slug')->all());

        $this->get('/artikel/tips-baru-servis')->assertOk()->assertSee('Isi.');
        $this->get('/artikel/tips-baru-servis-2')->assertNotFound(); // draf tidak tampil publik
    }

    public function test_ticket_create_and_edit_events_show_on_public_status_page(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/tiket', [
            'code' => 'dsk-2001', 'customer_name' => 'Rina Wulandari', 'device' => 'Printer Canon G3010',
            'events' => [
                ['title' => 'Diterima', 'state' => 'done'],
                ['title' => 'Ganti head', 'note' => 'Menunggu sparepart', 'state' => 'now'],
                ['title' => '', 'state' => 'next'], // baris kosong dibuang
                ['title' => 'Siap diambil', 'state' => 'next'],
            ],
        ])->assertSessionHasNoErrors();

        $ticket = ServiceTicket::where('code', 'DSK-2001')->firstOrFail();
        $this->assertSame(['Diterima', 'Ganti head', 'Siap diambil'], $ticket->events->pluck('title')->all());

        $this->get('/cek-status?tiket=DSK-2001')->assertOk()->assertSee('Ganti head')->assertSee('Rina W');

        $this->put("/admin/tiket/{$ticket->id}", [
            'code' => 'DSK-2001', 'customer_name' => 'Rina Wulandari', 'device' => 'Printer Canon G3010',
            'events' => [['title' => 'Diterima', 'state' => 'done'], ['title' => 'Selesai', 'state' => 'now']],
        ]);

        $this->assertSame(['Diterima', 'Selesai'], $ticket->fresh()->events->pluck('title')->all());

        $this->post('/admin/tiket', ['code' => 'DSK-2001', 'customer_name' => 'X', 'device' => 'Y'])
            ->assertSessionHasErrors('code');
    }

    public function test_profile_changes_override_site_config_and_can_be_reset(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/profil', [
            'name' => 'PT Contoh Baru', 'short' => 'Contoh Baru', 'tagline' => 'Tagline baru', 'phone' => '0812-0000-1111',
            'whatsapp' => '0812-0000-1111', 'email' => 'halo@contoh.test', 'city' => 'Depok', 'address' => 'Jl. Contoh 1, Depok',
            'area' => 'Jabodetabek', 'hours' => 'Setiap hari 09.00–17.00', 'maps_query' => 'Contoh Depok',
        ])->assertSessionHasNoErrors();

        $this->assertSame('628120000111'.'1', config('site.whatsapp'));

        $this->get('/kontak')->assertOk()->assertSee('halo@contoh.test')->assertSee('Setiap hari 09.00');

        // pada permintaan berikutnya (boot baru) nilai dibaca ulang dari database
        $this->assertSame('PT Contoh Baru', Setting::overrides()['name']);

        $this->post('/admin/kembalikan/profil')->assertSessionHasNoErrors();
        $this->assertSame('PT Digital Space Komputer', config('site.name'));
        $this->assertDatabaseMissing('settings', ['key' => 'name']);
    }

    public function test_invalid_whatsapp_is_rejected(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/profil', [
            'name' => 'A', 'short' => 'A', 'tagline' => 'A', 'phone' => '1', 'whatsapp' => '12', 'email' => 'a@b.test',
            'city' => 'A', 'address' => 'A', 'area' => 'A', 'hours' => 'A', 'maps_query' => 'A',
        ])->assertSessionHasErrors('whatsapp');
    }

    public function test_service_add_edit_move_and_delete_keep_segments_valid(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/layanan', [
            'title' => 'Instal CCTV', 'icon' => 'shield', 'short' => 'Pasang kamera.', 'price_category' => 'jaringan',
            'intro' => 'Pengantar CCTV.', 'problems' => "Pasang kamera\nSetting DVR",
        ])->assertSessionHasNoErrors();

        $this->get('/layanan/instal-cctv')->assertOk()->assertSee('Setting DVR');
        $this->get('/layanan')->assertOk()->assertSee('Instal CCTV');

        $this->post('/admin/layanan/instal-cctv/pindah', ['dir' => 'up']);
        $keys = array_keys(config('site.services'));
        $this->assertSame('instal-cctv', $keys[count($keys) - 2]);

        // hapus layanan yang dipakai tab beranda: tab tetap valid dan halaman tidak error
        $this->delete('/admin/layanan/service-printer')->assertSessionHasNoErrors();
        $this->assertArrayNotHasKey('service-printer', config('site.services'));
        foreach (config('site.segments') as $segment) {
            $this->assertNotContains('service-printer', $segment['services']);
            $this->assertNotEmpty($segment['services']);
        }

        $this->get('/')->assertOk();
        $this->get('/layanan/service-printer')->assertNotFound();
    }

    public function test_segments_and_landing_edits_render_on_site(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/tab-beranda', [
            'segments' => [
                'kantor' => ['label' => 'Perusahaan', 'services' => ['service-laptop-pc']],
                'rumah' => ['label' => 'Rumah', 'services' => ['macbook-imac']],
                'sekolah' => ['label' => 'Sekolah', 'services' => ['server-jaringan']],
            ],
        ])->assertSessionHasNoErrors();

        $this->get('/')->assertOk()->assertSee('Perusahaan');

        $this->put('/admin/landing/servis-panggilan', [
            'meta_title' => 'Judul Baru', 'meta_description' => 'Deskripsi baru.', 'headline' => 'Headline Baru?',
            'highlight' => 'Sorotan baru.', 'lead' => 'Lead baru.', 'checks' => "Satu\nDua", 'form_title' => 'Form Baru',
            'devices' => "Laptop\nPC", 'placeholder' => 'Contoh',
        ])->assertRedirect(route('admin.landings.index'));

        $this->get('/servis-panggilan')->assertOk()->assertSee('Judul Baru')->assertSee('Form Baru');

        $this->put('/admin/tab-beranda', ['segments' => ['kantor' => ['label' => 'X', 'services' => []]]])
            ->assertSessionHasErrors();
    }

    public function test_admin_users_cannot_delete_themselves_and_password_change_needs_current(): void
    {
        $me = User::factory()->create(['password' => 'password-lama-1']);
        $this->actingAs($me);

        $this->delete("/admin/pengguna/{$me->id}")->assertSessionHasErrors('users');
        $this->assertModelExists($me);

        $this->put('/admin/akun', ['name' => 'Aku', 'email' => $me->email, 'current_password' => 'salah', 'password' => 'password-baru-22', 'password_confirmation' => 'password-baru-22'])
            ->assertSessionHasErrors('current_password');

        $this->put('/admin/akun', ['name' => 'Aku', 'email' => $me->email, 'current_password' => 'password-lama-1', 'password' => 'password-baru-22', 'password_confirmation' => 'password-baru-22'])
            ->assertSessionHasNoErrors();

        $this->assertTrue(\Hash::check('password-baru-22', $me->fresh()->password));

        $this->post('/admin/pengguna', ['name' => 'Staf', 'email' => 'staf@contoh.test', 'role' => 'staf', 'password' => 'pendek', 'password_confirmation' => 'pendek'])
            ->assertSessionHasErrors('password');
    }
}
