<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Halaman pengaturan situs hanya untuk admin penuh; staf mendapat 403. */
class RequireAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Halaman ini hanya untuk admin.');

        return $next($request);
    }
}
