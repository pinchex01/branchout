<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Socialite;

class LoginController extends Controller
{
    protected $afterLogin  = '/home';
    protected $afterLogout  = '/';

    public function __construct()
    {
        parent::__construct();
    }

    public function getLogin()
    {
        \Auth::logout();
        return view('auth.login')
            ->with('page_title', 'Login');
    }

    public function login(Request $request)
    {
        $this->validateLoginCredentials($request);

        $credentials = $this->get_login_credentials_from_request($request);

        if (Auth::attempt($credentials)) {
            $next_url  =  session('next_url');
            return redirect()->to($next_url ? : $this->afterLogin);
        }

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'danger', 'message' => "Invalid username or password"]
            ]);

    }

    public function getRegister()
    {
        \Auth::logout();
        return view('auth.register')
            ->with('page_title', 'Create Account');
    }

    public function logout(Request $request)
    {
        \Auth::logout();
        $request->session()->forget([
            'next_url', 'user_merchants','_otp','current_merchant'
        ]);

        return redirect()->to($this->afterLogout);
    }

    protected function validateLoginCredentials(Request $request)
    {
        //allow user to login with id_number or email, this will check if the identity provided is an email
        //else it is a username..un comment this line to use either username or password..ensure username field exists in you table
        $identity = filter_var($request->get('username'), FILTER_VALIDATE_EMAIL)? 'email' : 'phone';

        $rules = [
            'username'=>"required|exists:users,$identity",
            'password'=>'required'
        ];

        $messages = [
            "$identity.exists" => 'Not found! Please register'
        ];

        $this->validate($request, $rules, $messages);
    }

    protected function get_login_credentials_from_request(Request $request)
    {
        $identity = filter_var($request->get('username'), FILTER_VALIDATE_EMAIL)? 'email' : 'phone';

        return $credentials = [
            $identity => $request->get('username'),
            'password' => $request->get('password'),
        ];
    }

    public function loginWithAuthorizeToken(Request $request)
    {
        \Auth::logout();

        $token  = $request->get('token','random_string');
        $username  = $request->get('username');

        $user = User::find_by_id_number($username);
        if(!$user || $user->authorize_token != $token)
            return redirect()->route('auth.login');

        $user->authorize_token  = null;
        $user->save();


        Auth::login($user);
        $next_url  =  $request->get('go_to','null');
        return redirect()->to($next_url ? : $this->afterLogin);
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function loginWithOtp(Request $request)
    {
        $ref  = $request->input('ref');
        $next_url  =  $request->get('go_to',null);

        #if user is already logged in, no need
        if(user()){
            return redirect()->to($next_url ? : $this->afterLogin);
        }

        #get user from ref and ensure ref and user exists
        if(!$ref || !$user = User::wherePk($ref)->first()){
            return redirect()->route('app.landing');
        }
        #if method is post, validate the otp
        if($request->method() == 'POST'){
            $action = $request->input('otp_action', 'verify');
            if($action == 'resend'){
                $this->set_otp($user, $request);
            }else{
                if($this->validate_signin_otp($request)){
                    Auth::login($user);

                    return redirect()->to($next_url ? : $this->afterLogin);
                }else{
                    return redirect()->back()
                        ->with('alerts', [
                            ['type' => 'danger', 'message' => "Invalid OTP"]
                        ]);
                }
            }
        }

        #check if otp session has been started, if not start one and show form
        $data = $this->get_otp_data($request);
        if(!$data)
            $data = $this->set_otp($user, $request);

        return view('auth.otp', [
            'user' => $user,
            'otp' => $data
        ])->with('page_title', "Sign In OTP");
    }

    /**
     * @param Request $request
     * @param bool $clear
     * @return bool
     */
    public function validate_signin_otp(Request $request, $clear  = true)
    {
        $key = $request->session()->getId();
        $data = session('otp_'.$key);

        $status = $data['code'] == $request->input('otp_code');
        if($status && $clear)
            $request->session()->forget('otp_'.$key);

        return $status;
    }

    /**
     * @param User $user
     * @param Request $request
     * @return array
     */
    public function set_otp(User $user, Request $request)
    {
        list($ref, $code) = $user->send_otp("authentication");
        session([ 'otp_'.$request->session()->getId() => [
            'code' => $code,
            'ref' => $ref
        ]]);

        return [$ref, $code];
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function get_otp_data(Request $request)
    {
        $key = $request->session()->getId();
        return session('otp_'.$key);
    }

    //Redirects to facebook login page
    public function facebookRedirect()
    {
        return \Socialite::driver('facebook')->redirect();
    }

    //Handles callbacks from facebook login service
    public function facebookCallback()
    {
        $providerUser = \Socialite::driver('facebook')->user();

        if($providerUser->getId())
        {
          $id = $providerUser->getId();
          $nickname = $providerUser->getNickname();
          $email = $providerUser->getEmail();
          $name = $providerUser->getName();

          $split_names = explode(" ", $name);
          $first_name = $split_names[0];
          $last_name = $split_names[1];

          //Since its social, we will need to get user by email
            //TODO: Handle blank phone number and gender
          $user = User::getOrCreate($id, [
              'first_name' => $first_name,
              'last_name' => $last_name,
              'id_number' => $id,
              'email' => $email,
          ], true);

          if (!$user) {
              return response()->json("failed_creating_user", 500);
          }
          else
          {
            $logged_in_user = Auth::loginUsingId($user->id);
            if (!$logged_in_user)
            {
                throw new Exception('Error logging in');
            }
            else
            {
                $next_url = session('next_url');
                return redirect()->to($this->afterLogin);
            }
          }

          //TODO: Authenticate user and redirect to page to ask for phone number.
            //Then loginWithOtp
        }
        else
        {
          //TODO: Redirect to sign in page with error: Invalid login attempt
          return redirect()->to('/auth/signin');
        }
    }

    //Redirects to google login page
    public function googleRedirect()
    {
        return \Socialite::driver('google')->redirect();
    }

    //Handles callbacks from google login service
    public function googleCallback()
    {
        $providerUser = \Socialite::driver('google')->stateless()->user();

        if($providerUser->getId())
        {
          $id = $providerUser->getId();
          $nickname = $providerUser->getNickname();
          $email = $providerUser->getEmail();
          $name = $providerUser->getName();

          $split_names = explode(" ", $name);
          $first_name = $split_names[0];
          $last_name = $split_names[1];

          //Since its social, we will need to get user by email
            //TODO: Handle blank phone number and gender
          $user = User::getOrCreate($id, [
              'first_name' => $first_name,
              'last_name' => $last_name,
              'id_number' => $id,
              'email' => $email,
          ], true);

          if (!$user) {
              return response()->json("failed_creating_user", 500);
          }
          else
          {
            $logged_in_user = Auth::loginUsingId($user->id);
            if (!$logged_in_user)
            {
                throw new Exception('Error logging in');
            }
            else
            {
                $next_url = session('next_url');
                return redirect()->to($this->afterLogin);
            }
          }

          //TODO: Authenticate user and redirect to page to ask for phone number.
            //Then loginWithOtp
        }
        else
        {
          //TODO: Redirect to sign in page with error: Invalid login attempt
          return redirect()->to('/auth/signin');
        }

    }

    public function changePassword(Request $request)
    {
        if($request->method() == 'POST'){
            $this->validate($request, [
                'password' => "required|min:6|confirmed",
            ]);

            $user = user();
            $user->password = $request->input('password');
            $user->change_password = 0;
            $user->save();

            user()->fresh();

            return redirect()->route('auth.login')
                ->with('alerts', [
                    ['type' => 'success', 'message' => "Password changed successfully!"]
                ]);
        }

        return view('auth.change')
            ->with('page_title', "Change Password");
    }
}
