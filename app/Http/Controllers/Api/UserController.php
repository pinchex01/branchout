<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Organiser;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function verifyPassword(Request $request)
    {
        //todo: probably make the user custom for other clients
        if (Hash::check($request->get('password'), user()->password)) {
            return response()->json([
                'status' => 'ok',
                'message' => 'User verified'
            ]);
        } else {
            //todo: throttle attempts
            return response()->json([
                'status' => 'fail',
                'message' => 'Incorrect password'
            ]);
        }

    }

    public function verifyIdWithName(Request $request)
    {
        $valid = \Validator::make($request->all(), [
            'id_number' => 'required',
            'first_name' => 'required|title',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => "Invalid input",
                'errors' => $valid->errors()
            ], 430);
        }

        //check if for id number locally and iprs
        $person = get_user_by_id_number($request->get('id_number'));

        if ($request->get('unregistered') && $person->registered) {
            return response()->json([
                'status' => 'fail',
                'message' => "User already registered"
            ], 430);
        }

        // if id number exists in iprs proceed
        if ($person) {
            //verify that first name provided matches the one retrieved
            if (strtolower($person->first_name) != strtolower($request->get('first_name')) && !config('app.iprs_pretend')) {
                return response()->json([
                    'status' => 'fail',
                    'message' => "ID number and First Name combination do not match"
                ], 430);
            }

            return response()->json([
                'status' => 'ok',
                'user' => [
                    'id_number' => $person->id_number,
                    'registered' => !!$person->registered
                ]
            ]);
        }

        //id number does not exists or could not be retrieved
        return response()->json([
            'status' => 'fail',
            'message' => "Sorry we can not verify the ID Number at the moment "
        ], 430);
    }

    /**
     * Check if user exists in eresident and iprs
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyIdNumber(Request $request)
    {
        $valid = \Validator::make($request->all(), [
            'id_number' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => "Invalid input",
                'errors' => $valid->errors()
            ], 403);
        }

        //check if for id number in eresident and iprs
        $person = get_user_by_id_number($request->get('id_number'));

        // if id number exists in iprs proceed
        if ($person) {
            return response()->json($person, 200);
        }

        //id number does not exists or could not be retrieved
        return response()->json(['error' => "user_not_found_with_iprs"], 401);
    }

    public function checkEmailUnique(Request $request)
    {
        $exists = User::whereEmail($request->input('email'))->first();
        if ($exists)
            return response()->json([
                'status' => 'fail',
                'message' => "Email already taken"
            ]);

        return response()->json([
            'status' => 'ok',
            'message' => "ok"
        ]);
    }

    public function checkPhoneUnique(Request $request)
    {
        $phone = encode_phone_number($request->input('phone'));
        $exists = User::wherePhone($phone)->first();
        if ($exists)
            return response()->json([
                'status' => 'fail',
                'message' => "Phone already taken"
            ]);

        return response()->json([
            'status' => 'success',
            'message' => "ok"
        ]);
    }

    /**
     * Create user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:users,id_number',
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => "required|full_phone|unique:users,phone",
            'password' => 'required|min:6',
        ], [
            'dob' => 'Please enter a valid date of birth',
            'username.unique' => "Username already taken. Maybe you have registered before, try singing in with your phone number"
        ]);



        $user = User::getOrCreate($request->input('username'),[
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'gender' => $request->input('gender'),
            'id_number' => $request->input('username'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => $request->input('password'),
            'gender' => $request->input('gender', 'Male')
        ], true);

        //user account was not created
        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => "Oopps! Something went terribly wrong!",
                'code' => 'failed_creating_user'
            ], 430);
        }

        $user->generateApiToken();

        return response()->json($user, 200);
    }

    /**
     * Check if user exists and return suer
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getUserWithIDAndName(Request $request)
    {
        $this->validate($request,[
            'id_number' => "required_with:first_name|valid_id_number:first_name",
            'first_name' => "required"
        ]);

        $user = get_user_by_id_number($request->input('id_number'));
        if (!$user)
            return response([
                'status' => 'fail',
                'message' => "User not with ID and First Name combination"
            ], 430);

        return response()->json($user, 200);
    }

    /**
     * Authenticated route for admins to add user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUser(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:users,id_number',
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => "required|full_phone|unique:users,phone",
            'password' => 'sometimes|min:6',
        ], [
            'dob' => 'Please enter a valid date of birth',
            'username.unique' => "Username already exists",
            'phone.*' => "Invalid phone number"
        ]);

        $user = User::getOrCreate($request->input('username'),$request->only(['first_name','last_name','email','phone','password','username']), true);

        //user account was not created
        if (!$user) {
            return response()->json([
                'status' => 'ok',
                'code' => "failed_creating_user",
                'message' => "Something went wrong. User not created"
            ], 430);
        }

        $user->generateApiToken();

        return response()->json($user, 200);
    }

    public function lookup(Request $request)
    {
        $this->validate($request, [
            'username' => "required|min:3"
        ]);

        $username = $request->input('username');
        $user = User::get_by_username($username);

        $data = [ "username" => $username, 'avatar' => ''];
        if(!$user){
            $data["registered"] = false;
        }else{
            $data['registered'] = true;
            $data['avatar'] = $user->getAvatar();
            $data['first_name'] = $user->first_name;
            $data['last_name'] = $user->last_name;
            $data['full_name'] = $user->full_name;
            $data['email'] = $user->email;
            $data['id_number'] = $user->id_number;
            $data['pk'] = $user->pk;
            $data['status'] = $user->status;
        }

        return response()->json([
            'status' => 'ok',
            'user' => $data
        ], 200);
    }

    public function signin(Request $request)
    {
        $this->validate($request, [
            'username' => "required",
            'password' => 'required'
        ]);

        $user = User::find_by_id_number($request->input('username'));
        if(!$user || !\Hash::check($request->input('password'), $user->password)){
          return response()->json([
              'status' => 'fail',
              'message' => "Invalid username or password"
          ], 430);
        }else{
          $next_url  =  session('next_url');
          $user->authorize_token =  md5(time().str_random());
          $user->save();
          return response()->json([
              'status' => 'ok',
              'user' => [
                  'pk' => $user->pk,
                  'first_name' => $user->first_name,
                  'last_name' => $user->last_name,
                  'other_name' => $user->other_name,
                  'full_name' => $user->full_name,
                  'id_number' => $user->id_number,
                  'phone' => $user->phone,
                  'email' => $user->email,
                  'token' => $user->api_token,
                  'avatar' => $user->getAvatar(),
                  'merchants' => $user->organisers
              ],
              'go_to' => urlencode($next_url ? : url('/home')),
              'authorize_token' => $user->authorize_token
          ]);
        }
    }

    public function getByUsername(Request $request)
    {
      $this->validate($request, [
        'username' => "required"
      ]);

      $username  = $request->input('username');
      $user = User::find_by_id_number($username);

      return response()->json($user, 200);
    }


    public function addUserToRole(Request $request)
    {
      $request->user()->can(['create-staffs']);

      $this->validate($request, [
        'user_id' => "required|exists:users,pk",
        'role_id' =>"required|exists:roles,id",
        'type' => "required|in:org,sys",
        'merchant_id' => "nullable|required_if:type,org|exists:organisers,id"
      ],[
        'type.*' => "Invalid type"
      ]);
      $user  = User::wherePk($request->input('user_id'))->first();
      $role_id  = $request->input('role_id');

      #add user to a system role
      if($request->input("type") == 'sys'){
        $user->addRoles($role_id);

        #add user to a merchant role
      }else{
        $merchant  =  Organiser::find($request->input('merchant_id'));
        $merchant->add_user($user, $role_id);
      }

      return response()->json([
        'status' => "ok",
        "message" => "User added to role successfully"
      ], 200);
    }
}
