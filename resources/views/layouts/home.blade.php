@extends('layouts.app')

@section('page_title',isset($page_title)? $page_title." |" : 'Event Tickets |')

@section('body')

    @include('partials.navbar')
    @if(user())
        @include('user.partials.header')
    @else
        @include('pages.partials.header')
    @endif

    @yield('content')
    <div class="container mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="navbar navbar-default navbar-fixed-bottom">
                    <div class="container">
                        <p class="navbar-text pull-left"> Party People Entertainment Limited © 2017. All rights reserved  - info@partypeople.co.ke </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
