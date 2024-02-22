<?php

namespace App\Http\Middleware;

use Closure;

class Backend
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
        //ensure user is administrator
        $user = $request->user();
        if (!$user->is_admin()){
            abort(403);
        }
        return $next($request);
    }
}
