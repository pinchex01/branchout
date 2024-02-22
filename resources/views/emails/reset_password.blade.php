@extends('layouts.email')

@section('message_content')
    Hello,<br><br>

    Your password has been reset.

    Login to <a href="http://partypeople.co.ke">partypeople.co.ke</a>
    Username: {{ $user->username }}
    Password: {{ $password }}

    You will be required to change your password.

    Thank you
@stop
