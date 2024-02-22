<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class OrganiserApiAuth
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
        $user = $this->validateRequest($request);
        if (!$user){
            return response()->json("unauthorized",401);
        }

        $request->merge(['user' => $user ]);

        //bind user to request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        if($this->check_if_has_access($request))
            return $next($request);

        abort(403);
    }

    public function check_if_has_access($request)
    {
        $user = $request->user();
        $merchant = $request->route()->parameter('organiser');

        #first fail if not merchant or user
        if(!$user || !$merchant){
            return false;
        }

        #get user merchants
        $merchants  = $user->organisers()->get();
        //ensure that use can access the merchant requested
        if(in_array($merchant->id, $merchants->where('status','active')->pluck('id')->toArray())) {
            $user->flushCache();

            return true;
        }

        return false;
        
    }

    private function validateRequest($request)
    {
        $validator = \Validator::make($request->all(),[
            'token' => "required",
            'key' => 'required|exists:users,id_number'
        ]);
        $status = null;
        if ($validator->fails()){
            $status = false;
        }else{
            $status = User::verifyApiToken($request->input('token'), $request->input('key'));
        }

        return $status;

    }
}
