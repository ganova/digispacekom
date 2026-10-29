<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Support\Site;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            route('home'), route('services.index'), route('about'), route('articles.index'),
            route('contact'), route('prices.index'),
        ])
            ->merge(collect(array_keys(Site::services()))->map(fn ($slug) => route('services.show', $slug)))
            ->merge(collect(array_keys(config('site.landings')))->map(fn ($slug) => route('landing', $slug)))
            ->merge(Article::published()->get()->map(fn ($a) => route('articles.show', $a)));

        return response()->view('sitemap', ['urls' => $urls], 200, ['Content-Type' => 'application/xml']);
    }
}
