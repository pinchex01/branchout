@extends('layouts.auth')

@section('body_class','body-login')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">
                            Validate OTP (One Time Passcode)
                        </h3>
                        <div class="tools pull-right">

                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <!-- /end panel-heading -->
                    <div class="panel-body">
                        @include('partials.notifier')
                        <div class="row">
                            <div class="col-md-12">
                                <p>
                                    A OTP(One Time Passcode) has been sent to your phone {{ mask_phone( $user->phone) }}.
                                    This is so as to protect your information and prevent unauthorized persons from stealing your ticket (*wink)
                                </p>
                                <br>
                                <p>Please enter the OTP below to proceed</p>
                                <form class="" method="post" autocomplete="off">
                                    {!! csrf_field() !!}
                                    <div class="form-group {{$errors->has('otp_code')? 'has-error': ''}}">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon3">{{ array_get($otp, 'ref') }} - </span>
                                            {!! Form::text('otp_code',null,['class'=>'form-control', 'autocomplete' => "off"]) !!}
                                        </div>
                                        {!! $errors->first('otp_code', '<span class="help-block">:message</span>') !!}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="otp_action" value="resend" class="btn btn-link"> Resend Code</button>
                                        <button type="submit" name="otp_action" value="verify" class="btn btn-primary">Validate OTP</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-xs-12 col-md-7">
                                <span>If you do not receive the OTP for more than 30 seconds, please click Resend</span>
                            </div>
                            <div class="col-xs-12 col-md-5 text-right">
                                <span class="text-right">Expires in: <span id="otp_timer" class=""></span> </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection