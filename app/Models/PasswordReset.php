<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Mail\PasswordResetNotification;
use App\Jobs\SendSms;

class PasswordReset extends Model
{
    protected $table  = 'password_resets';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
   public $incrementing = false;

    protected $fillable  = ['pk', 'username', 'mode', 'token'];

    public function user()
    {
      return $this->belongsTo(User::class, 'username', $this->mode);
    }

    /**
     * [create_new_request description]
     * @param  [type]  $mode     [description]
     * @param  [type]  $username [description]
     * @param  boolean $notify   [description]
     * @return [type]            [description]
     */
    public static function create_new_request($mode, $username, $notify = true)
    {
      #delete all requests related t the username
      self::remove_requests($username);

      #get token
      $token  = self::generate_token($mode);

      $reset = self::create([
        'username' => $username,
        'mode' => $mode,
        'token' => $token
      ]);

      if($notify)
        $reset->send_notification();

      return $reset;
    }

    public function send_notification()
    {
        if($this->mode =='phone')
          return $this->send_sms();

       return \Mail::to($this->user)->send(new PasswordResetNotification($this));
    }

    public static function generate_token($mode)
    {
      if($mode == 'phone')
        return rand(10000, 99999);

      return md5(time());
    }

    public static function remove_requests($username)
    {
      \DB::table('password_resets')->where(['username' => $username])->delete();
    }

    public static function boot()
    {
      parent::boot();

      static::creating(function($reset){
        $reset->pk = \Uuid::generate()->string;
      });
    }

    public function send_sms()
    {
        $code  = $this->token;
        #send sms for complete order
        $message = "Dear Customer, your activation code for password reset is {$code}";
        dispatch(new SendSms($this->username, $message));
    }
}
