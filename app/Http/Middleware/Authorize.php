<?php

namespace App\Http\Middleware;

use Closure;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, ...$abilities)
    {
        $allowed = collect($abilities)->some(function ($ability) {
            return app('gate')->allows($ability);
        });

        if ($allowed) return $next($request);

        return response()->json(['message' => 'User access forbidden'], 403);
    }
}
