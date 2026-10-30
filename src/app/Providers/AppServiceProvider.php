<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Konten dari CMS menimpa config/site.php
        foreach (Setting::overrides() as $key => $value) {
            config(["site.{$key}" => $value]);
        }
    }
}
