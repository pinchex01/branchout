@extends('layouts.auth')
<?php $vue = true ?>
@section('body_class','body-login')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">

                    <div class="panel-body">
                        <form method="post">
                            <h3 class="mt-30">Password Reset</h3>
                            <p>What is your new password</p>
                            @include('partials.notifier')
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <div class="form-actions mt20">
                                <button  type="submit" class="btn btn-danger pull-right" > Continue <i class="fa fa-arrow-right"></i></button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
