<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function getForgotView()
    {
        return view('auth.forgot')
          ->with('page_title', "Forgot My Password");
    }

    public function getUser(Request $request)
    {
      $this->validate($request, [
        'username' => "required"
      ]);

      $user = User::find_by_id_number($request->input('username'));

      if(!$user){
        return response()->json([
            'status' => 'fail',
            'message' => "Invalid username or password"
        ], 430);
      }else {
        return response()->json([
          'pk' => $user->pk,
          'masked_phone' => mask_phone($user->phone),
          'masked_email' => $user->email ? mask_email($user->email) : null
        ], 200);
      }

    }

    public function forgotPasswordRequest(Request $request)
    {
      $this->validate($request, [
        'user_ref' => "required|exists:users,pk",
        'mode' => "required|in:email,phone"
      ]);

      $mode = $request->input('mode');
      $user = User::wherePk($request->input('user_ref'))->first();
      $username  = $user->$mode;

      $reset  = PasswordReset::create_new_request($mode, $username, true);

      if (!$reset){
        return response()->json([
          'status' => 'fail',
          'message' => "Sorry cannot complete your request at the moment"
        ], 430);
      }

      return response()->json([
        'reset_pk' => $reset->pk,
        'username' => $reset->username,
        'mode' => $reset->mode
      ], 200);
    }

    public function validateResetCode(Request $request)
    {
      $this->validate($request, [
        'ref' => "required|exists:password_resets,pk",
        'code' => "required"
      ]);

      $reset  = PasswordReset::wherePk($request->input('ref'))->first();
      $code  = $request->input('code');

      if ($reset->token  != $code){
        return response()->json([
          'status' => 'fail',
          'message' => "Invalid reset code"
        ], 430);
      }

      return response()->json([
        'status' => 'ok',
        'message' => "Reset code accepted"
      ], 200);
    }

    public function validateResetToken($token, Request $request)
    {
        $reset  = PasswordReset::whereToken($token)->first();

        if (!$reset)
          return redirect()->route('auth.login');


        #get user from usersname
        $user  = User::find_by_id_number($reset->username);

        return view('auth.forgot', [ 'user' => $user, 'reset' => $reset ])
          ->with('page_title', "Reset your password");
    }

    public function changePassword(Request $request)
    {
      $this->validate($request, [
        'type' => "required|in:change,forgot",
        'user_ref' => "nullable|exists:users,pk",
        'reset_ref' => "nullable|required_if:type,forgot|exists:password_resets,pk",
        #require old password if user just wants to change password
        'old_password' => "nullable|required_if:type,change",
        'password' => "required|min:6|confirmed",
        'password_confirmation' => "required|same:password",
      ]);

      if($request->input('type') == 'forgot'){
        $reset  = PasswordReset::wherePk($request->input('reset_ref'))->first();

        #clear the reset request from db
        PasswordReset::remove_requests($reset->username);
      }

      $user = User::wherePk($request->input('user_ref'))->first();
      $old_password  = $request->input('old_password');
      $password  = $request->input('password');

      #dont worry about hashing, this will be done automatically
      $user->password  =  $password;
      $user->save();

      return response()->json([
        'status' => 'ok',
        'message' => "Password reset successfully"
      ], 200);
    }
}
