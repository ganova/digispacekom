<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Notify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/** Notifikasi email untuk permintaan masuk: penerima dan akun pengirim (SMTP) diatur klien sendiri. */
class MailController extends Controller
{
    public const KEYS = ['notify_emails', 'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_secure', 'mail_from'];

    public function edit(): View
    {
        return view('admin.settings.mail', ['custom' => Setting::isCustomized(...self::KEYS)]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->merge(['notify_list' => Notify::parse($request->input('notify_emails'))]);

        $data = $request->validate([
            'notify_list' => ['array', 'max:5'],
            'notify_list.*' => ['email', 'max:120'],
            'smtp_host' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9.-]+$/'],
            'smtp_port' => ['nullable', 'integer', 'between:1,65535'],
            'smtp_user' => ['nullable', 'string', 'max:190'],
            'smtp_pass' => ['nullable', 'string', 'max:190'],
            'smtp_secure' => ['required', 'in:tls,ssl'],
            'mail_from' => ['nullable', 'email', 'max:120'],
        ], [
            'notify_list.max' => 'Maksimal 5 alamat penerima.',
            'notify_list.*.email' => 'Alamat email penerima tidak valid: :input',
            'smtp_host.regex' => 'Server SMTP hanya boleh berisi huruf, angka, titik, dan tanda hubung. Contoh: smtp.gmail.com',
            'smtp_port.between' => 'Port harus antara 1 dan 65535.',
        ], ['smtp_host' => 'server SMTP', 'smtp_port' => 'port', 'mail_from' => 'email pengirim']);

        if ($data['notify_list'] && ! $request->filled('smtp_host')) {
            return back()->withInput()->withErrors(['smtp_host' => 'Isi server SMTP agar email bisa dikirim.']);
        }

        Setting::put('notify_emails', $data['notify_list'] ?: null);
        Setting::put('smtp_host', $data['smtp_host'] ?? null);
        Setting::put('smtp_port', isset($data['smtp_port']) ? (int) $data['smtp_port'] : null);
        Setting::put('smtp_user', $data['smtp_user'] ?? null);
        Setting::put('smtp_secure', $data['smtp_secure']);
        Setting::put('mail_from', $data['mail_from'] ?? null);

        // kolom password dikosongkan = tetap memakai yang tersimpan
        if ($request->boolean('smtp_pass_clear') || ! $request->filled('smtp_host')) {
            Setting::put('smtp_pass', null);
        } elseif ($request->filled('smtp_pass')) {
            Setting::put('smtp_pass', Crypt::encryptString($data['smtp_pass']));
        }

        return back()->with('ok', 'Pengaturan notifikasi email disimpan.');
    }

    public function test(): RedirectResponse
    {
        if (! Notify::ready()) {
            return back()->withErrors(['test' => 'Simpan dulu alamat penerima dan server SMTP.']);
        }

        Notify::configureMail();

        try {
            Mail::raw('Ini email percobaan dari CMS '.config('site.name').'. Bila Anda menerimanya, notifikasi permintaan masuk sudah berfungsi.', function ($m) {
                $m->to(Notify::recipients())->subject('Tes notifikasi - '.config('site.short'));
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['test' => 'Gagal mengirim: '.str($e->getMessage())->before("\n")->limit(220)]);
        }

        return back()->with('ok', 'Email percobaan terkirim ke '.implode(', ', Notify::recipients()).'. Cek kotak masuk (dan folder spam).');
    }

    public function reset(): RedirectResponse
    {
        Setting::reset(...self::KEYS);

        return back()->with('ok', 'Pengaturan email dikembalikan ke awal.');
    }
}
