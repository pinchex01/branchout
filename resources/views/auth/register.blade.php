@extends('layouts.auth')
<?php $vue  = true ?>
@section('body_class','body-login')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <user-registration-form></user-registration-form>
            </div>
        </div>
    </div>

@endsection