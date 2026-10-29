<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Support\Site;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'segments' => config('site.segments'),
            'articles' => Article::published()->limit(3)->get(),
        ]);
    }

    public function services(): View
    {
        return view('pages.services', ['services' => Site::services()]);
    }

    public function service(string $slug): View
    {
        $service = Site::service($slug) ?? abort(404);

        return view('pages.service', [
            'service' => $service,
            'others' => collect(Site::services())->except($slug)->take(3),
        ]);
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function landing(Request $request, string $landing): View
    {
        return view('landing.show', [
            'slug' => $landing,
            'page' => config("site.landings.{$landing}"),
            'device' => $request->query('perangkat'),
            'complaint' => $request->query('keluhan'),
        ]);
    }
}
