<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Organiser
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

        $merchant = $request->route('organiser');
        $allowed_merchants = $this->get_allowed_merchants($request);

        //ensure that use can access the merchant requested
        if(in_array($merchant->id, $allowed_merchants->where('status','active')->pluck('id')->toArray())) {
            $this->preload_merchant($request);
            $this->flush_user_role_cache();

            return $next($request);
        }

        abort(404);
    }

    public function preload_merchant(Request $request)
    {
        $organiser = organiser();

        //check if current merchant is not the same as the one cache, if not cache the new one
        if($request->session()->get('organiser') != $organiser){
            //preload merchant
            $request->session()->put('current_merchant', $organiser->load([]));
        }
        $this->merchant = $organiser;

        return $this;
    }

    /**
     * Flush user role cache and load role based on the route
     */
    public function flush_user_role_cache()
    {
        $user = user();
        $user->flushCache();
    }

    public function get_allowed_merchants(Request $request)
    {
        $user  = user();
        $merch = $request->route()->parameter('organiser');
        $merchants = session('user_merchants');

        if (!$merchants || !$merchants->where('slug', $merch->slug)->first()){
            $merchants = $user->organisers()->get();
            $request->session()->put('user_merchants', $merchants);
        }

        return $merchants;
    }
}
