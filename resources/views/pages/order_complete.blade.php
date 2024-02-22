@extends('layouts.page')

<?php $vue = true ?>

@section('body_class','body-login')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 order_header">
                <div class="text-center">
                    <i class="fa fa-check-circle fa-6x text-success"></i>
                </div>
                <h1>Thank you for buying tickets with us!</h1>
                <h2>
                    Your tickets for {{ $order->event }} and a
                    confirmation email have been sent to you. Check your spam folder as well
                </h2>
            </div>
        </div>
    </div>
@endsection

