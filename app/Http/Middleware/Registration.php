<?php

namespace Vanguard\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Registration
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
        // If registration is not enabled, we will
        // simply display 404 page instead of registration page
        if (! settings('reg_enabled')) {
            throw new NotFoundHttpException;
        }

        return $next($request);
    }
}
