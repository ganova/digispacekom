<?php

/*
| Pesan validasi berbahasa Indonesia. Nama kolom ditulis lewat parameter
| "attributes" pada tiap controller, atau dipakai apa adanya bila belum ada.
*/

return [
    'accepted' => ':attribute harus disetujui.',
    'array' => ':attribute harus berupa daftar.',
    'boolean' => ':attribute harus berisi ya atau tidak.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':attribute bukan tanggal yang valid.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'file' => ':attribute harus berupa berkas.',
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'integer' => ':attribute harus berupa angka bulat.',
    'mimes' => ':attribute harus berformat: :values.',
    'mimetypes' => ':attribute harus berformat: :values.',
    'numeric' => ':attribute harus berupa angka.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi.',
    'required_with' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah dipakai.',
    'uploaded' => ':attribute gagal diunggah. Pastikan ukurannya tidak melebihi batas.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, tanda minus, dan garis bawah.',

    'between' => [
        'array' => ':attribute harus berisi :min sampai :max butir.',
        'file' => 'Ukuran :attribute harus antara :min dan :max KB.',
        'numeric' => ':attribute harus antara :min dan :max.',
        'string' => ':attribute harus antara :min dan :max karakter.',
    ],
    'gte' => [
        'array' => ':attribute harus berisi minimal :value butir.',
        'file' => 'Ukuran :attribute minimal :value KB.',
        'numeric' => ':attribute tidak boleh lebih kecil dari :value.',
        'string' => ':attribute minimal :value karakter.',
    ],
    'max' => [
        'array' => ':attribute maksimal :max butir.',
        'file' => 'Ukuran :attribute maksimal :max KB.',
        'numeric' => ':attribute maksimal :max.',
        'string' => ':attribute maksimal :max karakter.',
    ],
    'min' => [
        'array' => ':attribute minimal :min butir.',
        'file' => 'Ukuran :attribute minimal :min KB.',
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
    ],

    'custom' => [],

    // nama kolom yang tidak diberi label khusus
    'attributes' => [
        'name' => 'Nama',
        'email' => 'Email',
        'password' => 'Password',
        'title' => 'Judul',
        'short' => 'Ringkasan',
        'intro' => 'Pengantar',
        'problems' => 'Daftar masalah',
        'icon' => 'Ikon',
        'category' => 'Kategori',
        'label' => 'Nama',
        'image' => 'Gambar',
        'cover' => 'Gambar sampul',
        'stock' => 'Stok',
    ],
];
