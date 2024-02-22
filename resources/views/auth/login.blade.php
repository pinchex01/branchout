@extends('layouts.auth')
<?php $vue = true ?>
@section('body_class','body-login')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">

                    <div class="panel-body">
                        @include('partials.notifier')
                        <user-auth-form></user-auth-form>
                        <p>
                          <a href="{{ route('auth.forgot')}}">Forgot your password?</a>
                        </p>
                    </div>
                    <div class="panel-footer">
                        Don't have an account? <a href="{{ route ('auth.register')}}"> Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
