<?php

use App\Models\PaymentLog;

/**
 *  Commonly used functions
 * ------------------------------
 */


if (!function_exists('user')){
    /**
     *  Get currently logged in user
     *  use: user()->name or user('name')
     *
     * @param null $key
     * @return App\Models\User
     */
    function user($key = null)
    {
        $user = \Auth::user();
        if (!is_null($key))
            return $user->$key;

        return $user;
    }
}

function iprs_lookup($id_number, $pretend = false){
    //if pretend, generate from faker
    if ($pretend){
        $faker = \Faker\Factory::create();

        $first_name  = $faker->firstName;
        $last_name  = $faker->lastName;
        $other_name = $faker->lastName;
        $full_name = implode(' ',[$other_name, $first_name,$last_name]);

        return (Object)[
            'first_name'=>$first_name,
            'last_name'=>$last_name,
            'other_name'=>$other_name,
            'name'=>$full_name,
            'full_name'=>$full_name,
            'id_number'=>$id_number,
            'gender'=>'Male' ,
            'dob'=>$faker->date(),
            'avatar'=>'',
            'citizenship'=>'Kenyan',
        ];
    }



    $url = "http://197.248.4.134/iprs/databyid?number=".urlencode($id_number);
    $response = \Httpful\Request::get($url)
        ->authenticateWithBasic('mrmiddleman','6I2-u?=W')
        ->send();

    //return false for any response other than 200
    if ($response->code != 200) return false;

    $body = $response->body;

    //id number does not exist
    if (!$body->valid) return false;

    //iprs is down
    if($body->valid && object_get($body,'error',false)) return false;

    $dob = new \DateTime($body->dob);
    return (Object)[
        'first_name'=>$body->first_name,
        'last_name'=>$body->surname,
        'other_name'=>$body->other_name,
        'name'=>$body->surname." ".$body->first_name." ".$body->other_name,
        'full_name'=>$body->surname." ".$body->first_name." ".$body->other_name,
        'id_number'=>$body->id_number,
        'gender'=>$body->gender == "M" ? "Male" :"Female" ,
        'dob'=>$dob->format('Y-m-d'),
        'avatar'=>$body->photo,
        'citizenship'=>$body->citizenship,
    ];
}

if(!function_exists('get_user_by_id_number')){
    function get_user_by_id_number($number,$iprs = true)
    {
        //remove leading zeros
        $number = ltrim($number,'0');

        //initialize user
        $user = null;

        // 1 if user is registered in eresident else 0
        $registered = 1;

        /*
         * First search local database for the given id number
         * if user is found the return user info
         * with key registered = 1
         */
        $rs = \App\Models\User::find_by_id_number($number);
        if ($rs) $user =  (Object)$rs->toArray();

        /*
         * Search IPRS database for the given id number only if iprs search is enabled
         * if user is found the return user info
         * with key registered = 0
         */
        if (!$user && $iprs) {
            $registered = 0;
            $res  = iprs_lookup($number,config('app.iprs_pretend'));
            if ($res) $user = $res;
        }

        if ($user) {
            $user->registered = $registered;
            return $user;
        }


        /*
         * id number does not exists
         */
        return false;
    }
}

if (!function_exists('match_props_to_params')){
    /**
     * Match props to param
     *
     * @param $props
     * @param array $params
     *
     * @return array
     */
    function map_props_to_params($props, array $params, $strict = true)
    {
        //check if props is object and convert to array
        $props  = is_object($props) ? (array) $props : $props;

        //match param keys to props values
        $data = [];
        array_map(function ($param) use ($props, &$data, $strict){
            $field  = array_get($props,$param);
            if($strict && !$field){
                return ;
            }else{
                return $data[$param] = $field ? : null;
            }
        }, $params);

        return $data;
    }
}

if (!function_exists('encode_phone_number')){
    /**
     * @param $number
     * @param string $code
     * @return mixed|string
     */
    function encode_phone_number($number,$code = '254')
    {
        // remove preceding plus if it exists
        $number = preg_replace('/^\+/', '', $number);

        if (starts_with($number,$code))
            return $number;

        if (starts_with($number,'07')){
            $real = substr($number,1);
            return $code.$real;
        }else{
            return $code.$number;
        }
    }
}

if (!function_exists('hash_payload')){
    /**
     * Generate a hash string using sha256
     *
     * @param $payload
     * @param $key
     * @return string
     */
    function hash_payload($payload, $key)
    {
        $hash = hash_hmac('sha256', $payload, $key);

        return $hash;
    }
}
if (!function_exists('menu_current_route')){
    /**
     * Check if current ulr matches a certain route pattern the add active state to menu
     * @param $pattern
     * @param string $active_string
     * @param string $false
     * @return bool|string
     */
    function menu_current_route($pattern,$active_string = 'active',$false=''){

        if (is_array($pattern)){
            foreach($pattern as $item){
                if (\Route::is($item)){
                    echo $active_string;
                    return;
                }
            }
        }else{
            if (\Route::is($pattern)){
                echo $active_string;
                return;
            }
        }

        return;
    }
}


if (!function_exists('menu_current_path')){

    /**
     * @param $pattern
     * @param $params
     * @param string $active_string
     * @param string $false
     * @return string
     */
    function menu_current_path($pattern, $params, $active_string = 'active', $false='')
    {
        $url  = (route($pattern,$params));

        //dd($url ==  request()->fullUrl());

        if (request()->fullUrl() == $url) {
            return $active_string;
        }


        return $false;
    }
}

if(!function_exists('mask_phone')){

    /**
     * @param $phone_number
     * @param string $char
     * @return mixed
     */
    function mask_phone($phone_number, $char = '*')
    {
        //ensure it is a valid email
        $len = strlen($phone_number);

        $str = maskString($phone_number, 6, $len - 2);

        return $str;
    }
}


if (!function_exists('current_route_is')){
    /**
     * Check if the current route matches given patterns
     *
     * @param $pattern
     * @return bool
     */
    function current_route_is($pattern){

        if (is_array($pattern)){
            foreach($pattern as $item){
                if (\Route::is($item)){
                    return true;
                }
            }
        }else{
            if (\Route::is($pattern)){
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('route_domain')){

    function route_domain($pattern)
    {
        $domain = '';
        $route = current_route_is($pattern);
        if ( $route == \Route::is('organiser.*'))
            $domain = 'organiser';

        elseif($route == \Route::is('account.*'))
            $domain = 'user';

        elseif ($route == \Route::is('admin.*'))
            $domain = 'admin';

        elseif ($route == \Route::is('auth.*'))
            $domain = 'auth';

        return $domain;
    }
}

if (!function_exists('organiser')){

    /**
     * Get the current organiser from route
     *
     * @param null $key
     *
     * @return \App\Models\Organiser|null
     */
    function organiser($key = null)
    {
        if(!current_route_is('organiser.*'))
            return null;

        $organiser = request()->route('organiser');

        if ($key)
            return $organiser->$key;

        return $organiser;
    }
}

if(!function_exists('sign_pesaflow_payload')){
    /**
     * Sign pesaflow payload
     *
     * @param $payload array
     * @param $key string
     * @return string
     */
    function sign_pesaflow_payload(array $payload, $key)
    {
        $payload_string = join("",$payload);

        $hash = hash_hmac('sha256', $payload_string, $key);

        return base64_encode($hash);
    }
}

if (!function_exists('money')) {
    /**
     * Format a given amount to the given currency
     *
     * @param $amount
     * @param $currency
     * @return string
     */
    function money($amount, $currency = 'KES', $decimal = 0)
    {
        return $currency." " . number_format($amount, $decimal);
    }
}

if (!function_exists('is_empty')){
    /**
     * @param $var
     * @return bool
     */
    function is_empty($var)
    {
        if (is_array($var) && count($var) > 0 )
            return false;
        $var = trim($var,' ');
        if (is_null($var)) return true;
        if (empty($var)) return true;

        return false;
    }
}

if(!function_exists('maskString')){
    /**
     * Mask part of a string
     *
     * <code>
     * echo maskString('4012888888881881', 6, 4, '*');
     * </code>
     *
     * @param   string  $s      String to process
     * @param   integer $start  Number of characters to leave at start of string
     * @param   integer $end    Number of characters to leave at end of string
     * @param   string  $char   Character to mask string with
     * @return  string
     */
    function maskString($s, $start=1, $end=null, $char = '*') {
        $start = $start - 1;

        $array = str_split($s);

        $end  = strlen($s) < $end ? strlen($s) : $end ? : strlen($s);

        for ($start; $start < $end; $start++) {
            $array[$start] = $char;
        }
        return join('',$array);
    }
}

if(!function_exists('set_sql_mode'))
{
    /**
     * @param string $mode
     * @return bool
     */
    function set_sql_mode($mode = '')
    {
        return \DB::statement("SET SQL_MODE=''");
    }
}
if(!function_exists('map_directions_link')) {
    /**
     * Google maps directions map
     *
     * @param $location
     * @param $latitude
     * @param $longitude
     * @return string
     */
    function map_directions_link($location,$latitude,$longitude)
    {
        return "https://maps.google.com/maps?ll={$latitude},{$longitude}&amp;z=16&amp;t=m&amp;hl=en-US&amp;gl=PT&amp;mapclient=embed&amp;daddr={$location}@{$latitude},{$longitude}";
    }
}

if(!function_exists('map_large_link')) {

    /**
     * Google maps map view
     *
     * @param $location
     * @param $latitude
     * @param $longitude
     * @return string
     */
    function map_large_link($location,$latitude,$longitude)
    {
        return "https://maps.google.com/maps?ll={$latitude},{$longitude}&amp;z=16&amp;t=m&amp;hl=en-US&amp;gl=PT&amp;mapclient=embed&amp;";
    }
}
if(!function_exists('mask_email')){
    /**
     * @param $email
     * @param string $char
     * @return string
     */
    function mask_email($email, $char = '*')
    {
        //ensure it is a valid email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            list ($username, $domain) = explode('@',$email);
            $length  = strlen($username);

            if ($length > 3)
                return maskString($username,3,strlen($username),$char)."@".$domain;

            return maskString($username,2,strlen($username),$char)."@".$domain;
        }

        return $email;
    }
}

if(!function_exists('mask_phone')){
    /**
     * @param $phone_number
     * @param string $char
     * @return string
     */
    function mask_phone($phone_number, $char = '*')
    {
        //ensure it is a valid email
        $len = strlen($phone_number);

        $str = maskString($phone_number, 6, $len - 2);

        return $str;
    }
}


if (!function_exists('current_route_is')){
    /**
     * Check if the current route matches given patterns
     *
     * @param $pattern
     * @return bool
     */
    function current_route_is($pattern){

        if (is_array($pattern)){
            foreach($pattern as $item){
                if (\Route::is($item)){
                    return true;
                }
            }
        }else{
            if (\Route::is($pattern)){
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('organiser')){

    /**
     * Get the current organiser from route. If route is not a organiser route, return null
     *
     * @param null $key
     *
     * @return \App\Models\Organiser|null|mixed
     */
    function organiser($key = null)
    {
        if(!current_route_is(['organiser.*','agent.*']))
            return null;

        if(current_route_is('organiser.*'))
            $merchant= request()->route('organiser');
        elseif(current_route_is('agent.*'))
            $merchant= request()->route('agent');

        if ($key)
            return $merchant->$key;

        return $merchant;
    }
}

if(!function_exists('acronym')){
    /**
     * Get acronym from string
     *
     * @param $string
     * @return string
     */
    function acronym($string)
    {
        //first slug string to take care of double spaces
        $words = explode("-", str_slug($string));
        $acronym = "";

        foreach ($words as $w) {
            $acronym .= $w[0];
        }

        //return uppercase acronym
        return strtoupper($acronym);
    }
}
if (!function_exists('save_settings')){
    /**
     * Sync settings and save changes
     * also log any changes done if changed by a logged in user
     * @param array $settings
     * @return array
     */
    function save_settings(array  $settings)
    {
        //if user is not active do not log changes
        if ($user  = user()){
            $logs = [];
            foreach ($settings as $key => $value){
                //changed
                if (!settings($key) || settings($key) != $value){
                    $old_val = settings($key);
                    $logs[] = "$user changed setting: '{$key}' from '{$old_val}' to '{$value}'";
                }
            }
        }

        //sync settings and save
        \Setting::set($settings);
        \Setting::save();

        //log any changes
        if (!empty($logs))
            event(new \App\Events\SettingsChanged($logs, $user));

        return $settings;
    }
}

if (!function_exists('settings'))
{
    /**
     * @param string|array $key|$input
     * @param null $default
     * @return mixed
     */
    function settings($key,$default = null)
    {
        if (is_array($key)) {
            return save_settings($key);
        }
        return \Setting::get($key,$default);
    }
}

if(!function_exists('generate_application_no')){
    /**
     * Generate unique application numbers
     *
     * @return string
     */
    function generate_application_no()
    {
        $token = 'O-AA00001';
        $last = \DB::table('applications')->orderBy('id','DESC')->lock()->first();

        //todo: optimize this function
        if ($last) {
            $token = ++$last->application_no;
        }
        return $token;
    }
}

if(!function_exists('move_temp_file')){
    /**
    * Use this to move temp file from temp to a permanent location to avoid being deleted
    * @param $to
    * @param $to
    *
    *@return string|null
    */
    function move_temp_file($file, $to)
    {
        if (\Storage::disk('local')->exists($file)){
            $path = \Storage::disk('local')->move($file, $to);

            return $path? : $to;
        }
        return null;
    }
}
if(!function_exists('get_purchasable_from_ref_no')){
    function get_purchasable_from_ref_no($ref_no, array $extra = null)
    {
        $order = \App\Models\Order::find_by_order_no(array_get($extra, 'username', $ref_no));
        if ($order)
            return ['order', $order];

        $user = \App\Models\User::getOrCreate($ref_no, $extra);
        if ($user)
            return ['user', $user];

        return null;
    }
}

if(!function_exists('get_filters_from_request')){

  function get_filters_from_request($request)
  {
    $filters = $request->get('filters', []);

    return $filters;
  }
}


if (!function_exists('generate_random_string')){
  function generate_random_string($length = 5, $int = false, $caps = true)
  {
      $num = $int ?  "0123456789" : "";
      $s_caps  = !$caps ? "abcdefghijklmnopqrstuvwxyz" : "";
      return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ{$s_caps}{$num}"), 0, $length);
  }
}


if (!function_exists('log_mpesa_payment_request')){
  function log_mpesa_payment_request($request)
  {
      try {
          $payment = \App\Models\PaymentLog::create([
            'txn_no' => $request->get('trx_ref'),
            'username' => $request->get('phone'),
            'ref' => $request->get('id_number'),
            'amount' => $request->get('amount'),
            'status' => 'pending'
          ]);

          return $payment;
      } catch (\Illuminate\Database\QueryException $e) {
        return null;
      }
  }
}

if (!function_exists('create_pesaflow_bill')){
    function create_pesaflow_bill($invoice_no, $amount, $notes, \App\Models\User $user, $currency = 'KES', $return = 0)
    {
        #get all pesaflow related config
        $config = config('pesaflow');

        #generate secure hash
        $hash = sign_pesaflow_payload([
          $config['apiClientId'], intval(round($amount)), $config['apiServiceId'], $user->id_number,
          $currency, $invoice_no, $notes, $user->full_name, $config['apiSecret']
        ], $config['apiKey']);

        $params = [
            'apiClientID' => $config['apiClientId'],
            'secureHash' => $hash,
            'currency' => $currency,
            'billDesc' => $notes,
            'billRefNumber' => $invoice_no,
            'serviceID' => $config['apiServiceId'],
            'clientMSISDN' => $user->phone,
            'clientName' => $user->full_name,
            'clientIDNumber' => $user->id_number,
            'clientEmail' => $user->email,
            'amountExpected' => $amount,
            'callBackURLOnSuccess' => route('app.orders.view', [ $invoice_no]),
            'pictureURL' => $user->getAvatar(),
            'notificationURL' => route('api.pesaflow-ipn', [ $invoice_no]),
            'callBackURLOnFail' => route('app.orders.view', [ $invoice_no, 'status' =>'fail']),
            'return' => $return
        ];

        $url = $config['url'];
        $response = Httpful\Request::post($url, http_build_query($params))
            ->sendsType(Httpful\Mime::FORM)
            ->send();


        if (!$response->code == 200 || !$response->body) {
            return null;
        }

        $result = preg_replace('~[\r\n]+~', '', strip_tags($response->body));
        $result = trim($result);

        return is_empty($result) ? null : $result;
    }
}
