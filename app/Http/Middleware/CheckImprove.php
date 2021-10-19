<?php

namespace App\Http\Middleware;

use Closure;

class CheckImprove
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! is_null($request->user())) {
            if (!$request->user()->is_improve) {
                return redirect('/save');
            }
        }
        return $next($request);
    }
}
