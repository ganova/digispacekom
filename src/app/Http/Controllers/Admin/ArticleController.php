<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Support\Images;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $articles = Article::query()
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->input('q').'%'))
            ->latest('updated_at')->paginate(20)->withQueryString();

        return view('admin.articles.index', ['articles' => $articles]);
    }

    public function create(): View
    {
        return view('admin.articles.form', ['article' => new Article(['category' => 'Tips'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($failed = $this->saveCover($request, $data)) {
            return $failed;
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')->with('ok', 'Artikel disimpan.');
    }

    public function edit(Article $article): View
    {
        return view('admin.articles.form', ['article' => $article]);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $this->validated($request, $article);
        $old = $article->cover;

        if ($failed = $this->saveCover($request, $data, $old)) {
            return $failed;
        }

        $article->update($data);

        if ($old && $old !== $article->cover) {
            Images::delete('articles', $old);
        }

        return redirect()->route('admin.articles.index')->with('ok', 'Artikel diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Images::delete('articles', $article->cover);
        $article->delete();

        return redirect()->route('admin.articles.index')->with('ok', 'Artikel dihapus.');
    }

    private function validated(Request $request, ?Article $article = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('articles', 'slug')->ignore($article?->id)],
            'category' => ['nullable', 'string', 'max:60'],
            'excerpt' => ['required', 'string', 'max:300'],
            'body' => ['required', 'string', 'max:200000'],
            'cover_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:6144'],
            'publish' => ['required', 'in:draft,now,scheduled'],
            'published_at' => ['nullable', 'date', 'required_if:publish,scheduled'],
        ], [
            'cover_file.mimes' => 'Gambar sampul harus berformat JPG, PNG, atau WebP.',
            'cover_file.max' => 'Ukuran gambar sampul maksimal 6 MB.',
        ], [
            'cover_file' => 'gambar sampul', 'title' => 'judul', 'slug' => 'alamat (slug)', 'category' => 'kategori', 'excerpt' => 'ringkasan',
            'body' => 'isi artikel', 'published_at' => 'waktu terbit',
        ]);

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title'], $article);

        $data['published_at'] = match ($data['publish']) {
            'draft' => null,
            'scheduled' => $data['published_at'],
            // artikel yang sudah terbit tidak berubah tanggalnya saat diedit
            default => $article?->published_at && $article->published_at->isPast() ? $article->published_at : now(),
        };

        unset($data['publish']);

        return $data;
    }

    /** Sampul baru menggantikan yang lama; kotak "hapus sampul" mengosongkannya. */
    private function saveCover(Request $request, array &$data, ?string $current = null): ?RedirectResponse
    {
        unset($data['cover_file']);
        $data['cover'] = $request->boolean('cover_remove') ? null : $current;

        if ($file = $request->file('cover_file')) {
            try {
                $data['cover'] = Images::store($file, 'articles', 1400);
            } catch (\Throwable $e) {
                report($e);

                return back()->withInput()->withErrors(['cover_file' => 'Gambar tidak bisa disimpan: folder unggahan tidak dapat ditulis. Hubungi admin server.']);
            }
        }

        return null;
    }

    private function uniqueSlug(string $source, ?Article $article): string
    {
        $base = Str::slug($source) ?: 'artikel';
        $slug = $base;
        $i = 2;

        while (Article::where('slug', $slug)->when($article, fn ($q) => $q->where('id', '!=', $article->id))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
