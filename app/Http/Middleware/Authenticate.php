<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param  Closure(Request): (Response)  $next
     * @param  mixed  ...$guards
     * @return Response
     */
    public function handle($request, Closure $next, ...$guards): Response
    {
        if ($this->auth->guard($guards)->check()) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
