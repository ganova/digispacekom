<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BrandAndAboutTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    private string $uploads;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uploads = storage_path('framework/testing/brand-'.uniqid());
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

    private function profile(array $extra = []): array
    {
        return $extra + [
            'name' => 'PT Contoh', 'short' => 'Contoh', 'tagline' => 'Tagline', 'phone' => '0812-0000-1111',
            'whatsapp' => '0812-0000-1111', 'email' => 'a@contoh.test', 'city' => 'Bekasi', 'address' => 'Jl. Contoh 1',
            'area' => 'Jabodetabek', 'hours' => '08.00-17.00', 'maps_query' => 'Contoh Bekasi',
        ];
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_default_site_uses_the_placeholder_logo(): void
    {
        $this->get('/')->assertOk()->assertSee('DIGITAL SPACE')->assertDontSee('lg-img', false);
    }

    public function test_company_logo_replaces_placeholder_in_header_and_footer(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/profil', $this->profile(['logo' => $this->png()]))->assertRedirect()->assertSessionHasNoErrors();

        $logo = config('site.logo');
        $this->assertNotNull($logo);
        $this->assertFileExists($this->uploads.'/brand/'.$logo);

        // tanpa logo gelap: logo terang dipakai di kotak putih untuk latar gelap
        $this->get('/')->assertOk()
            ->assertSee('uploads/brand/'.$logo, false)
            ->assertSee('lg-chip', false);

        $this->put('/admin/profil', $this->profile(['logo_dark' => $this->png('gelap.png')]))->assertRedirect()->assertSessionHasNoErrors();
        $dark = config('site.logo_dark');
        $this->assertNotNull($dark);
        $this->assertSame($logo, config('site.logo')); // logo terang dipertahankan

        $this->get('/')->assertSee('uploads/brand/'.$dark, false)->assertDontSee('lg-chip', false);
    }

    public function test_logo_can_be_removed_and_old_files_are_deleted(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/profil', $this->profile(['logo' => $this->png()]))->assertRedirect();
        $first = config('site.logo');

        $this->put('/admin/profil', $this->profile(['logo' => $this->png('baru.png')]))->assertRedirect();
        $this->assertNotSame($first, config('site.logo'));
        $this->assertFileDoesNotExist($this->uploads.'/brand/'.$first);

        $current = config('site.logo');
        $this->put('/admin/profil', $this->profile(['logo_remove' => '1']))->assertRedirect();
        $this->assertNull(config('site.logo'));
        $this->assertFileDoesNotExist($this->uploads.'/brand/'.$current);
        $this->get('/')->assertSee('DIGITAL SPACE');
    }

    public function test_logo_rejects_unsafe_files(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/profil', $this->profile(['logo' => UploadedFile::fake()->createWithContent('x.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>1</script></svg>')]))
            ->assertSessionHasErrors('logo');
        $this->put('/admin/profil', $this->profile(['logo' => UploadedFile::fake()->createWithContent('x.php', '<?php echo 1;')]))
            ->assertSessionHasErrors('logo');
        $this->assertNull(config('site.logo'));
    }

    public function test_about_vision_and_mission_show_on_about_and_contact_pages(): void
    {
        $this->get('/kontak')->assertOk()->assertSee('Visi')->assertSee('Misi')->assertSee('Menjadi mitra IT tepercaya');

        $this->actingAs($this->admin());
        $this->get('/admin/tentang')->assertOk()->assertDontSee('<x-admin', false);

        $this->put('/admin/tentang', [
            'about_lead' => 'Kami teknisi komputer sejak 2015.',
            'about_body' => "Paragraf satu.\n\nParagraf dua.",
            'vision' => 'Menjadi nomor satu di Bekasi.',
            'mission' => "Melayani cepat\nMenjaga kualitas",
        ])->assertSessionHasNoErrors();

        $this->get('/tentang')->assertOk()
            ->assertSee('Kami teknisi komputer sejak 2015.')
            ->assertSee('Paragraf dua.')
            ->assertSee('Menjadi nomor satu di Bekasi.')
            ->assertSee('Menjaga kualitas')
            ->assertDontSee('Menjadi mitra IT tepercaya');

        $this->get('/kontak')->assertOk()
            ->assertSee('Menjadi nomor satu di Bekasi.')
            ->assertSee('Melayani cepat');

        // reset kembali ke isi awal
        $this->post('/admin/kembalikan/tentang')->assertSessionHasNoErrors();
        $this->get('/kontak')->assertSee('Menjadi mitra IT tepercaya');
    }

    public function test_about_form_validates_and_is_admin_only(): void
    {
        $this->actingAs($this->admin());
        $this->put('/admin/tentang', ['about_lead' => '', 'vision' => '', 'mission' => ''])
            ->assertSessionHasErrors(['about_lead', 'vision', 'mission']);
        $this->put('/admin/tentang', ['about_lead' => 'x', 'vision' => 'y', 'mission' => "\n  \n"])
            ->assertSessionHasErrors('mission');

        $this->actingAs(User::factory()->create(['role' => 'staf']));
        $this->get('/admin/tentang')->assertForbidden();
        $this->put('/admin/tentang', ['about_lead' => 'x', 'vision' => 'y', 'mission' => 'z'])->assertForbidden();
    }

    public function test_highlighter_animation_is_gone_from_price_page_styles(): void
    {
        $css = file_get_contents(public_path('css/pricing.css'));

        $this->assertStringNotContainsString('hlIn', $css);
        $this->assertStringNotContainsString('.hl::after', $css);
    }
}
