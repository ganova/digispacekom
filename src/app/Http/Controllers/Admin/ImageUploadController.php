<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Images;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Unggah gambar dari editor artikel; mengembalikan alamat gambar untuk disisipkan ke isi artikel. */
class ImageUploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'mimes:png,jpg,jpeg,webp', 'max:6144'],
        ], [
            'image.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 6 MB.',
            'image.uploaded' => 'Gambar gagal diunggah. Pastikan ukurannya tidak lebih dari 6 MB.',
        ], ['image' => 'Gambar']);

        try {
            $name = Images::store($request->file('image'), 'articles/content', 1400);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'Gambar tidak bisa disimpan: folder unggahan tidak dapat ditulis.'], 500);
        }

        return response()->json(['url' => '/uploads/articles/content/'.$name, 'name' => $name]);
    }
}
