<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure(Request): (Response) $next
     * @param mixed ...$roles
     * @return mixed|Response|void
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (auth()->user() && in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        return response()->json(['message' => 'You don\'t have access to this method'], 403);
    }
}
