<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        return view('articles.index', ['articles' => Article::published()->paginate(9)]);
    }

    public function show(Article $article): View
    {
        abort_if(! $article->published_at || $article->published_at->isFuture(), 404);

        return view('articles.show', [
            'article' => $article,
            'more' => Article::published()->whereKeyNot($article->id)->limit(3)->get(),
        ]);
    }
}
