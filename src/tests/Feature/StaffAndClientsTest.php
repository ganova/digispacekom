<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class StaffAndClientsTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /** PNG 1x1 valid. */
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    private string $uploads;

    protected function setUp(): void
    {
        parent::setUp();

        // jangan menyentuh logo asli di public/uploads saat tes dijalankan
        $this->uploads = storage_path('framework/testing/clients-'.uniqid());
        config(['site.upload_root' => $this->uploads]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->uploads);
        parent::tearDown();
    }

    private function png(string $name = 'logo.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(self::PNG));
    }

    public function test_staff_can_only_reach_articles_tickets_prices_and_own_account(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staf']));

        foreach (['/admin', '/admin/artikel', '/admin/artikel/create', '/admin/tiket', '/admin/tiket/create', '/admin/harga', '/admin/harga/create', '/admin/akun'] as $url) {
            $this->get($url)->assertOk();
        }

        foreach (['/admin/permintaan', '/admin/permintaan/unduh', '/admin/layanan', '/admin/tab-beranda', '/admin/landing', '/admin/klien',
            '/admin/profil', '/admin/daftar', '/admin/pengguna', '/admin/pengguna/baru'] as $url) {
            $this->get($url)->assertForbidden();
        }

        $this->put('/admin/profil', ['name' => 'Dibajak'])->assertForbidden();
        $this->post('/admin/pengguna', ['name' => 'X', 'email' => 'x@contoh.test', 'role' => 'admin', 'password' => 'password-panjang-1', 'password_confirmation' => 'password-panjang-1'])
            ->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'x@contoh.test']);
    }

    public function test_staff_menu_hides_admin_pages_but_admin_sees_everything(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staf']))->get('/admin')
            ->assertOk()
            ->assertSee('Tiket servis')
            ->assertSee('Daftar harga')
            ->assertDontSee('Permintaan masuk')
            ->assertDontSee('Landing page iklan')
            ->assertDontSee('Pengguna &amp; peran', false);

        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin')
            ->assertSee('Permintaan masuk')
            ->assertSee('Klien &amp; logo', false)
            ->assertSee('Pengguna &amp; peran', false);
    }

    public function test_staff_can_do_their_work(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staf']));

        $this->post('/admin/harga', ['category' => 'pc', 'name' => 'Ganti thermal paste', 'price_min' => 75000])
            ->assertRedirect(route('admin.prices.index'));
        $this->post('/admin/artikel', ['title' => 'Artikel Staf', 'excerpt' => 'x', 'body' => '<p>x</p>', 'publish' => 'draft'])
            ->assertRedirect(route('admin.articles.index'));
        $this->post('/admin/tiket', ['code' => 'DSK-3001', 'customer_name' => 'Lia', 'device' => 'PC', 'events' => [['title' => 'Diterima', 'state' => 'now']]])
            ->assertRedirect();

        $this->assertDatabaseHas('price_items', ['name' => 'Ganti thermal paste']);
        $this->assertDatabaseHas('articles', ['title' => 'Artikel Staf']);
        $this->assertDatabaseHas('service_tickets', ['code' => 'DSK-3001']);
    }

    public function test_admin_manages_users_and_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->post('/admin/pengguna', ['name' => 'Staf Baru', 'email' => 'staf@contoh.test', 'role' => 'staf', 'password' => 'password-panjang-1', 'password_confirmation' => 'password-panjang-1'])
            ->assertRedirect(route('admin.users.index'));

        $staff = User::where('email', 'staf@contoh.test')->firstOrFail();
        $this->assertFalse($staff->isAdmin());

        $this->put("/admin/pengguna/{$staff->id}", ['name' => 'Staf Baru', 'email' => 'staf@contoh.test', 'role' => 'admin'])
            ->assertSessionHasNoErrors();
        $this->assertTrue($staff->fresh()->isAdmin());

        $this->put("/admin/pengguna/{$staff->id}", ['name' => 'Staf Baru', 'email' => 'staf@contoh.test', 'role' => 'staf', 'password' => 'password-baru-22', 'password_confirmation' => 'password-baru-22'])
            ->assertSessionHasNoErrors();
        $this->assertTrue(\Hash::check('password-baru-22', $staff->fresh()->password));

        // satu-satunya admin tidak bisa diturunkan atau dihapus
        $staff->update(['role' => 'staf']);
        $this->put("/admin/pengguna/{$admin->id}", ['name' => $admin->name, 'email' => $admin->email, 'role' => 'staf'])
            ->assertSessionHasErrors('role');
        $this->assertTrue($admin->fresh()->isAdmin());

        $this->put("/admin/pengguna/{$admin->id}", ['name' => 'Baru', 'email' => $admin->email, 'role' => 'admin'])->assertSessionHasNoErrors();
        $this->assertSame('Baru', $admin->fresh()->name);

        $this->delete("/admin/pengguna/{$staff->id}");
        $this->assertModelMissing($staff);
    }

    public function test_client_logos_upload_show_on_site_and_get_cleaned_up(): void
    {
        $this->actingAs(User::factory()->create());

        $this->put('/admin/klien', [
            'clients' => [
                ['name' => 'PT Maju Jaya', 'logo' => $this->png()],
                ['name' => 'Sekolah Harapan'],
                ['name' => '   '], // baris kosong dibuang
            ],
        ])->assertSessionHasNoErrors();

        $clients = \App\Support\Site::clients();
        $this->assertCount(2, $clients);
        $this->assertNotNull($clients[0]['logo']);
        $this->assertNull($clients[1]['logo']);
        $this->assertFileExists($this->uploads.'/clients/'.($clients[0]['logo']));

        $this->get('/')->assertOk()
            ->assertSee('uploads/clients/'.$clients[0]['logo'], false)
            ->assertSee('Sekolah Harapan');
        $this->get('/tentang')->assertOk()->assertSee('PT Maju Jaya');
        $this->get('/servis-panggilan')->assertOk()->assertSee('uploads/clients/'.$clients[0]['logo'], false);

        // mempertahankan logo lama, mengganti nama
        $logo = $clients[0]['logo'];
        $this->put('/admin/klien', ['clients' => [['name' => 'PT Maju Jaya Abadi', 'logo_current' => $logo], ['name' => 'Sekolah Harapan']]])
            ->assertSessionHasNoErrors();
        $this->assertSame($logo, \App\Support\Site::clients()[0]['logo']);
        $this->assertSame('PT Maju Jaya Abadi', \App\Support\Site::clients()[0]['name']);

        // menandai hapus logo menghapus berkasnya
        $this->put('/admin/klien', ['clients' => [['name' => 'PT Maju Jaya Abadi', 'logo_current' => $logo, 'logo_remove' => '1']]])
            ->assertSessionHasNoErrors();
        $this->assertNull(\App\Support\Site::clients()[0]['logo']);
        $this->assertFileDoesNotExist($this->uploads.'/clients/'.($logo));
    }

    public function test_client_logo_rejects_dangerous_files_and_foreign_filenames(): void
    {
        $this->actingAs(User::factory()->create());

        $this->put('/admin/klien', ['clients' => [['name' => 'A', 'logo' => UploadedFile::fake()->createWithContent('x.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>')]]])
            ->assertSessionHasErrors('clients.0.logo');
        $this->put('/admin/klien', ['clients' => [['name' => 'A', 'logo' => UploadedFile::fake()->createWithContent('x.php', '<?php echo 1;')]]])
            ->assertSessionHasErrors('clients.0.logo');
        $this->put('/admin/klien', ['clients' => [['name' => 'A', 'logo_current' => '../../.env']]])
            ->assertSessionHasErrors('clients.0.logo_current');

        // nama berkas yang tidak dimiliki daftar tidak diterima sebagai logo
        $this->put('/admin/klien', ['clients' => [['name' => 'A', 'logo_current' => 'milik-orang-lain.png']]])->assertSessionHasNoErrors();
        $this->assertNull(\App\Support\Site::clients()[0]['logo']);
    }

    public function test_default_clients_still_render_as_plain_names_and_reset_works(): void
    {
        $this->get('/')->assertOk()->assertSee('KPU Jakarta Pusat');

        $this->actingAs(User::factory()->create());
        $this->put('/admin/klien', ['clients' => [['name' => 'Hanya Satu']]]);
        $this->assertCount(1, \App\Support\Site::clients());

        $this->post('/admin/kembalikan/klien')->assertSessionHasNoErrors();
        $this->assertCount(5, \App\Support\Site::clients());
        $this->assertSame('Embassy of India Jakarta', \App\Support\Site::clients()[0]['name']);
    }

    public function test_public_pages_ship_the_theme_switcher(): void
    {
        $this->get('/')->assertOk()
            ->assertSee("localStorage.getItem('site-theme')", false)
            ->assertSee('css/dark.css', false)
            ->assertSee('id="theme-tg"', false);
    }
}
