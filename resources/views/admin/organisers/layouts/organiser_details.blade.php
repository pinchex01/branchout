@extends('layouts.admin')

@section('page')

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-success" data-step="1" data-intro="{{ trans('intro.user.total_paid') }}" data-position='right'>
                <div class="card-icon bg-success text-center">
                    <i class="fa fa-calendar-plus-o"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{number_format($summary->get("total_events"))}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Events</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-danger" data-step="2" data-intro="{{ trans('intro.user.total_arrears') }}" data-position='right'>
                <div class="card-icon bg-danger text-center">
                    <i class="fa fa-ticket"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{number_format($summary->get("tickets_sold"))}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Tickets Sold</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="card bd-primary" data-step="3" data-intro="{{ trans('intro.user.total_services') }}" data-position='right'>
                <div class="card-icon bg-primary text-center">
                    <i class="fa fa-money"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{ money($summary->get("total_sales")) }}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Sales Volume</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel with-nav-tabs panel-default">
        <div class="panel-heading panel-n-tab-heading hidden-xs">
            <h3 class="panel-title pull-left">Details</h3>
            <div class="pull-right">
                @stack('organiser_actions')
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="{{menu_current_route('admin.organisers.view','active')}}">
                    <a href="{{ route('admin.organisers.view', [$organiser->id]) }}">
                        <i class="fa fa-sticky-note"></i> <strong>Details</strong></a>
                </li>
                <li class="{{menu_current_route('admin.organisers.events','active')}}">
                    <a href="{{ route('admin.organisers.events', [$organiser->id]) }}">
                        <i class="fa fa-building"></i> <strong>Events</strong></a>
                </li>
                <li class="{{menu_current_route('admin.organisers.bank-accounts','active')}}">
                    <a href="{{ route('admin.organisers.bank-accounts', [$organiser->id]) }}">
                        <i class="fa fa-list"></i> <strong>Bank Accounts</strong></a>
                </li>
                <li class="{{menu_current_route('admin.organisers.users','active')}}">
                    <a href="{{ route('admin.organisers.users', [$organiser->id]) }}">
                        <i class="fa fa-users"></i> <strong>Users</strong></a>
                </li>
            </ul>
        </div>
        <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
            <h3 class="panel-title">@yield('organiser_panel_title')</h3>
        </div>
        <div class="panel-body">
            @yield('organiser_info')
        </div>
    </div>

    @stack('organiser_panels')

@stop
