<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('excerpt', 300);
            $table->longText('body');
            $table->string('category', 60)->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('price_items', function (Blueprint $table) {
            $table->id();
            $table->string('category', 30)->index();
            $table->string('name');
            $table->unsignedInteger('price_min');
            $table->unsignedInteger('price_max')->nullable();
            $table->string('price_suffix', 40)->nullable();
            $table->string('note', 120)->nullable();
            $table->string('keywords')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('service_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('customer_name');
            $table->string('device');
            $table->string('complaint')->nullable();
            $table->timestamps();
        });

        Schema::create('service_ticket_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_ticket_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('note')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->string('state', 10)->default('done'); // done | now | next
            $table->unsignedTinyInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('booking'); // booking | penawaran
            $table->string('source', 60)->nullable();
            $table->string('name')->nullable();
            $table->string('organization')->nullable();
            $table->string('device')->nullable();
            $table->text('complaint')->nullable();
            $table->string('location')->nullable();
            $table->unsignedSmallInteger('units')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('service_ticket_events');
        Schema::dropIfExists('service_tickets');
        Schema::dropIfExists('price_items');
        Schema::dropIfExists('articles');
    }
};
