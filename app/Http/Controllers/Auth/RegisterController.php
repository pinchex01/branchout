<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    protected $redirectTo  = '/home';

    public function __construct()
    {
        parent::__construct();
        \Auth::logout();
    }

    public function getRegister()
    {
        return view('auth.register')
            ->with("page_title", "Create Account");
    }
}
