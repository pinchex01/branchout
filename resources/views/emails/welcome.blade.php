@extends('layouts.email')

@section('message_content')
    Hello,<br><br>

    Your account has been created successfully.

    Login to <a href="http://partypeople.co.ke">partypeople.co.ke</a>
    Username: {{ $user->username }}
    @if ($password)
        Password: {{ $password }}
    @endif

    
    Thank you
@stop
