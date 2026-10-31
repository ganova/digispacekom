<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('cover', 60)->nullable()->after('category'); // gambar sampul
        });

        Schema::table('price_items', function (Blueprint $table) {
            $table->string('image', 60)->nullable()->after('note');   // foto item/suku cadang
            $table->string('stock', 10)->nullable()->after('image');  // ready | habis | kosong = tidak ditampilkan
        });
    }

    public function down(): void
    {
        Schema::table('price_items', function (Blueprint $table) {
            $table->dropColumn(['image', 'stock']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('cover');
        });
    }
};
