<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengaturan konten yang diubah lewat CMS; menimpa nilai bawaan di config/site.php
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique();
            $table->longText('value');
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('status', 15)->default('baru')->index()->after('type'); // baru | dihubungi | selesai | batal
            $table->text('admin_note')->nullable()->after('units');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['status', 'admin_note']);
        });

        Schema::dropIfExists('settings');
    }
};
