<?php

namespace App\Mail;

use App\Models\PasswordReset;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $reset;
    public $password_reset_link;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PasswordReset $reset)
    {
        $this->reset = $reset;
        $this->password_reset_link = route('auth.reset.token', $reset->token);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.password_reset')
            ->subject("Password Reset Link");
    }
}
