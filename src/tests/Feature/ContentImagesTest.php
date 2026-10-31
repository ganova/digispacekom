<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\PriceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ContentImagesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    private string $uploads;

    protected function setUp(): void
    {
        parent::setUp();

        // jangan menyentuh gambar asli di public/uploads saat tes dijalankan
        $this->uploads = storage_path('framework/testing/img-'.uniqid());
        config(['site.upload_root' => $this->uploads]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->uploads);
        parent::tearDown();
    }

    private function png(string $name = 'foto.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(self::PNG));
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staf']);
    }

    // ---------- Layanan ----------

    public function test_service_can_be_added_without_problem_list_and_errors_are_indonesian(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/layanan', ['title' => 'CCTV', 'icon' => 'camera', 'short' => 'Smart CCTV AI Server', 'intro' => 'Pasang dan rawat CCTV.', 'problems' => ''])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.services.index'));

        $this->get('/layanan/cctv')->assertOk()->assertDontSee('Masalah umum, solusi cepat');
        $this->get('/layanan')->assertOk()->assertSee('CCTV');

        $this->post('/admin/layanan', ['icon' => 'camera', 'short' => 'x', 'intro' => 'x'])->assertSessionHasErrors('title');
        $message = session('errors')->first('title');
        $this->assertStringContainsString('wajib diisi', $message);
        $this->assertStringNotContainsString('field is required', $message);
    }

    // ---------- Tab beranda ----------

    public function test_home_tabs_can_be_added_reordered_and_removed(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/layanan', ['title' => 'CCTV', 'icon' => 'camera', 'short' => 'Kamera pengawas', 'intro' => 'x']);
        $this->post('/admin/layanan', ['title' => 'Game Station', 'icon' => 'gamepad', 'short' => 'Rental PS', 'intro' => 'x']);

        $this->put('/admin/tab-beranda', ['segments' => [
            ['key' => 'kantor', 'label' => 'Kantor & instansi', 'services' => ['service-laptop-pc']],
            ['key' => 'rumah', 'label' => 'Rumah', 'services' => ['macbook-imac']],
            ['label' => 'CCTV', 'services' => ['cctv']],
            ['label' => 'Game Station', 'services' => ['game-station', 'service-laptop-pc']],
        ]])->assertSessionHasNoErrors();

        $segments = config('site.segments');
        $this->assertSame(['kantor', 'rumah', 'cctv', 'game-station'], array_keys($segments));

        $this->get('/')->assertOk()->assertSee('Game Station')->assertSee('seg-cctv', false);

        // hapus tab dan urutan baru
        $this->put('/admin/tab-beranda', ['segments' => [
            ['key' => 'cctv', 'label' => 'CCTV', 'services' => ['cctv']],
            ['key' => 'kantor', 'label' => 'Kantor', 'services' => ['service-laptop-pc']],
        ]])->assertSessionHasNoErrors();
        $this->assertSame(['cctv', 'kantor'], array_keys(config('site.segments')));

        // tab kosong atau tanpa layanan ditolak
        $this->put('/admin/tab-beranda', ['segments' => [['label' => 'Kosong', 'services' => []]]])->assertSessionHasErrors('segments');
        $this->put('/admin/tab-beranda', ['segments' => []])->assertSessionHasErrors('segments');
        $this->assertSame(['cctv', 'kantor'], array_keys(config('site.segments')));
    }

    // ---------- Kategori harga ----------

    public function test_price_categories_can_be_added_by_staff_and_used_publicly(): void
    {
        $this->actingAs($this->staff());

        $this->get('/admin/kategori-harga')->assertOk()->assertSee('Sparepart');
        $this->post('/admin/kategori-harga', ['label' => 'Sparepart Ready'])->assertSessionHasNoErrors();

        $this->assertSame('Sparepart Ready', PriceItem::categories()['sparepart-ready']);

        $this->post('/admin/harga', ['category' => 'sparepart-ready', 'name' => 'RAM DDR4 8 GB', 'price_min' => 450000, 'stock' => 'ready'])
            ->assertRedirect(route('admin.prices.index'));

        $this->get('/cek-harga')->assertOk()->assertSee('Sparepart Ready')->assertSee('RAM DDR4 8 GB');
        $this->get('/cek-harga?kategori=sparepart-ready')->assertOk()->assertSee('RAM DDR4 8 GB')->assertDontSee('Laptop mati total');
        $this->get('/cek-harga?q=ram')->assertSee('Ready stok');

        // nama bisa diubah, kategori yang dipakai tidak bisa dihapus
        $this->put('/admin/kategori-harga/sparepart-ready', ['label' => 'Suku Cadang Ready'])->assertSessionHasNoErrors();
        $this->assertSame('Suku Cadang Ready', PriceItem::categories()['sparepart-ready']);

        $this->delete('/admin/kategori-harga/sparepart-ready')->assertSessionHasErrors('categories');
        $this->assertArrayHasKey('sparepart-ready', PriceItem::categories());

        PriceItem::where('category', 'sparepart-ready')->delete();
        $this->delete('/admin/kategori-harga/sparepart-ready')->assertSessionHasNoErrors();
        $this->assertArrayNotHasKey('sparepart-ready', PriceItem::categories());
    }

    public function test_deleting_a_category_unlinks_services_that_pointed_to_it(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/kategori-harga', ['label' => 'CCTV']);
        $this->post('/admin/layanan', ['title' => 'Pasang CCTV', 'icon' => 'camera', 'short' => 'x', 'intro' => 'x', 'price_category' => 'cctv']);
        $this->get('/layanan/pasang-cctv')->assertOk()->assertSee('Lihat estimasi harga');

        $this->delete('/admin/kategori-harga/cctv')->assertSessionHasNoErrors();

        $this->assertNull(config('site.services.pasang-cctv.price_category'));
        $this->get('/layanan/pasang-cctv')->assertOk()->assertDontSee('Lihat estimasi harga');
    }

    public function test_items_with_a_missing_category_do_not_break_the_public_page(): void
    {
        PriceItem::create(['category' => 'hilang', 'name' => 'Item yatim', 'price_min' => 1000]);

        $this->get('/cek-harga?q=yatim')->assertOk()->assertSee('Item yatim');
    }

    // ---------- Foto harga / suku cadang ----------

    public function test_price_item_photo_upload_replace_remove_and_delete(): void
    {
        $this->actingAs($this->staff());

        $this->post('/admin/harga', ['category' => 'sparepart', 'name' => 'SSD 256 GB', 'price_min' => 350000, 'stock' => 'ready', 'photo' => $this->png()])
            ->assertSessionHasNoErrors();

        $item = PriceItem::where('name', 'SSD 256 GB')->firstOrFail();
        $this->assertNotNull($item->image);
        $this->assertFileExists($this->uploads.'/prices/'.$item->image);

        $this->get('/cek-harga?q=ssd')->assertSee('uploads/prices/'.$item->image, false);

        $first = $item->image;
        $this->put("/admin/harga/{$item->id}", ['category' => 'sparepart', 'name' => 'SSD 256 GB', 'price_min' => 350000, 'photo' => $this->png('baru.png')]);
        $item->refresh();
        $this->assertNotSame($first, $item->image);
        $this->assertFileDoesNotExist($this->uploads.'/prices/'.$first);

        $this->put("/admin/harga/{$item->id}", ['category' => 'sparepart', 'name' => 'SSD 256 GB', 'price_min' => 350000, 'photo_remove' => '1']);
        $this->assertNull($item->fresh()->image);

        // memperbarui tanpa foto baru mempertahankan foto lama
        $this->put("/admin/harga/{$item->id}", ['category' => 'sparepart', 'name' => 'SSD', 'price_min' => 1000, 'photo' => $this->png()]);
        $kept = $item->fresh()->image;
        $this->put("/admin/harga/{$item->id}", ['category' => 'sparepart', 'name' => 'SSD 512', 'price_min' => 1000]);
        $this->assertSame($kept, $item->fresh()->image);

        $this->delete("/admin/harga/{$item->id}");
        $this->assertFileDoesNotExist($this->uploads.'/prices/'.$kept);
    }

    public function test_price_photo_rejects_non_images(): void
    {
        $this->actingAs($this->staff());

        $this->post('/admin/harga', ['category' => 'pc', 'name' => 'X', 'price_min' => 1, 'photo' => UploadedFile::fake()->createWithContent('x.php', '<?php echo 1;')])
            ->assertSessionHasErrors('photo');
        $this->post('/admin/harga', ['category' => 'pc', 'name' => 'X', 'price_min' => 1, 'photo' => UploadedFile::fake()->createWithContent('x.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>1</script></svg>')])
            ->assertSessionHasErrors('photo');
        $this->assertDatabaseMissing('price_items', ['name' => 'X']);
    }

    // ---------- Gambar artikel ----------

    public function test_article_cover_shows_on_site_and_is_cleaned_up(): void
    {
        $this->actingAs($this->staff());

        $this->post('/admin/artikel', ['title' => 'Panduan Memilih SSD', 'excerpt' => 'Ringkas.', 'body' => '<p>Isi.</p>', 'publish' => 'now', 'cover_file' => $this->png()])
            ->assertRedirect(route('admin.articles.index'));

        $article = Article::where('slug', 'panduan-memilih-ssd')->firstOrFail();
        $this->assertNotNull($article->cover);
        $this->assertFileExists($this->uploads.'/articles/'.$article->cover);

        $this->get('/artikel')->assertOk()->assertSee('uploads/articles/'.$article->cover, false);
        $this->get('/artikel/panduan-memilih-ssd')->assertOk()
            ->assertSee('class="art-hero', false)
            ->assertSee('og:image', false);

        $old = $article->cover;
        $this->put("/admin/artikel/{$article->slug}", ['title' => 'Panduan Memilih SSD', 'excerpt' => 'Ringkas.', 'body' => '<p>Isi.</p>', 'publish' => 'now', 'cover_file' => $this->png('b.png')]);
        $this->assertNotSame($old, $article->fresh()->cover);
        $this->assertFileDoesNotExist($this->uploads.'/articles/'.$old);

        $current = $article->fresh()->cover;
        $this->delete("/admin/artikel/{$article->fresh()->slug}");
        $this->assertFileDoesNotExist($this->uploads.'/articles/'.$current);
    }

    public function test_article_without_cover_keeps_the_generated_cover(): void
    {
        $this->get('/artikel')->assertOk()->assertSee('art-cover')->assertDontSee('has-img');
    }

    public function test_inline_image_upload_returns_a_public_url(): void
    {
        $this->actingAs($this->staff());

        $response = $this->postJson('/admin/unggah-gambar', ['image' => $this->png()])->assertOk();

        $this->assertStringContainsString('/uploads/articles/content/', $response->json('url'));
        $this->assertFileExists($this->uploads.'/articles/content/'.$response->json('name'));

        $this->postJson('/admin/unggah-gambar', ['image' => UploadedFile::fake()->createWithContent('x.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>')])
            ->assertStatus(422)->assertJsonValidationErrors('image');
        $this->postJson('/admin/unggah-gambar', [])->assertStatus(422);
    }

    public function test_inline_image_upload_requires_login(): void
    {
        $this->postJson('/admin/unggah-gambar', ['image' => $this->png()])->assertUnauthorized();
    }

    public function test_large_photos_are_resized_and_converted_to_webp(): void
    {
        if (! function_exists('imagewebp') || ! function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD dengan dukungan WebP tidak tersedia.');
        }

        $this->actingAs($this->staff());

        $path = tempnam(sys_get_temp_dir(), 'img').'.png';
        $im = imagecreatetruecolor(2600, 1300);
        imagefill($im, 0, 0, imagecolorallocate($im, 30, 90, 200));
        imagepng($im, $path);

        $name = $this->postJson('/admin/unggah-gambar', ['image' => new UploadedFile($path, 'besar.png', 'image/png', null, true)])
            ->assertOk()->json('name');

        $this->assertStringEndsWith('.webp', $name);
        [$w, $h] = getimagesize($this->uploads.'/articles/content/'.$name);
        $this->assertSame(1400, $w);
        $this->assertSame(700, $h);
        $this->assertLessThan(filesize($path), filesize($this->uploads.'/articles/content/'.$name));

        @unlink($path);
    }
}
