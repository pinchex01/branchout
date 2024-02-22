@extends('layouts.app')

@section('page_title',isset($page_title)? $page_title." - " : ' Backend |')

@section('body_class','body')

@section('body')
    <header>
        <!-- Primary navbar-->
        @include('partials.navbar')

        @include('admin.partials.header')
    </header>

    <div class="container">

        <div class="user-panel">
            <div class="row">
                <div class="col-lg-2 col-md-2 hidden-sm hidden-xs">
                    @include('admin.partials.sidebar')
                </div>

                <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                    <div>
                        @include('partials.notifier')
                    </div>
                    @yield('widgets')
                    @yield('page')
                </div>

            </div>
        </div>

    </div> <!-- /container -->
@endsection
