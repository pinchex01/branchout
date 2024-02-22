@inject('Application','\App\Models\Application')
<?php $vue = true ?>
@extends('layouts.admin')



@section('page')
    <div class="panel with-nav-tabs panel-default">
        <div class="panel-heading panel-n-tab-heading hidden-xs">
            <h3 class="panel-title pull-left">@yield('task_title')</h3>
            <div class="pull-right">
                @stack('application_actions')
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-heading">
            @include('admin.applications._tasks_menu_header')
        </div>
        <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
            <h3 class="panel-title">@yield('task_title')</h3>
        </div>
        <div class="panel-body">
            @yield('applications')
        </div>
    </div>
@stop

