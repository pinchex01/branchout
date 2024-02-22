<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()){
                \JavaScript::put([
                    'User' => [
                        'id_number' => user()->id_number,
                        'api_token' => user()->api_token,
                        'first_name' => user()->first_name,
                        'phone' => user()->phone
                    ]
                ]);
            }

            return $next($request);
        });
    }
}
