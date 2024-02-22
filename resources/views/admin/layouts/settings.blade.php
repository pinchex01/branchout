@extends('layouts.admin')

@section('page')
    <div class="panel with-nav-tabs panel-default">
        <div class="panel-heading panel-n-tab-heading hidden-xs">
            <h3 class="panel-title">{{ $page_title or 'Manage Site Settings' }}</h3>
        </div>
        <div class="panel-heading">
            @include('admin.settings._settings_menu')
        </div>
        <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
            <h3 class="panel-title">{{ $page_title or 'Manage Site Settings' }}</h3>
        </div>
        <div class="panel-body">
            @yield('settings')
        </div>
    </div>

    @yield('panels')
@endsection

