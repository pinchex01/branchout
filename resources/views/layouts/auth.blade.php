@extends('layouts.app')

@section('page_title',isset($page_title)? $page_title." |" : 'Event Tickets |')

@section('body')

    @include('partials.navbar')

    @yield('content')
@endsection