@extends('layouts.auth')
<?php $vue = true ?>
@section('body_class','body-login')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">

                    <div class="panel-body">
                        <forgot-password-form @isset($user) change="'change'" user_pk="{{ $user->pk }}" reset_pk="{{ $reset->pk}}" @endisset></forgot-password-form>
                    </div>
                    <div class="panel-footer">
                        Already have an account? <a href="{{ route ('auth.login')}}"> Sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
