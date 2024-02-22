@extends('layouts.email')

@section('message_content')
    Hello,<br><br>

    Click on the link below to reset your password. If you did not request for a password reset, ignore this message.

    <p>
      <a href="{{ $password_reset_link }}">Reset Password</a>
    </p>

    Thank you
@stop
