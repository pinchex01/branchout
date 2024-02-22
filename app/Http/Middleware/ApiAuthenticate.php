<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ApiAuthenticate
{/**
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

        return $next($request);
    }

    private function validateRequest(Request $request)
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
