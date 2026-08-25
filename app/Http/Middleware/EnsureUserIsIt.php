<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsIt
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isIt(), 403, 'Akses workspace hanya tersedia untuk Admin atau Super Admin.');
        return $next($request);
    }
}
