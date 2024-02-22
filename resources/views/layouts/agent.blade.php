@extends('layouts.app')

@section('page_title',isset($page_title)? $page_title." |" : ' My Account |')

@section('body_class','body')

@section('body')
    <header>
        <!-- Primary navbar-->
        @include('partials.navbar')

        @include('user.partials.header')
    </header>

    <div class="container">

        <div class="user-panel">
            <div class="row">
                <div class="col-lg-2 col-md-2 hidden-sm hidden-xs">
                    @include('user.partials.sidebar')
                </div>

                <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                    <div>
                        @include('partials.notifier')
                    </div>
                    <h3 class="visible-xs mb-10 text-right">AGENT No: <strong>{{ $organiser->code }}</strong></h3>
                    <div class="panel panel-default panel-parent hidden-xs">
                        <div class="panel-heading">
                            Agent Dashboard
                            <div class="pull-right">

                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="media col-md-12 media-pro mb-20">

                                <div class="media-body">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            Organiser Details
                                        </div>
                                        <div class="panel-body">
                                            <div class="media">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-condensed" width="100%">
                                                        <thead>
                                                        <tr class="primary">
                                                            <th>Name</th>
                                                            <th>Phone No.</th>
                                                            <th>Email</th>
                                                            <th>Agent No.</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>{{$organiser->name}}</td>
                                                            <td>{{$organiser->phone}}</td>
                                                            <td>{{$organiser->email}}</td>
                                                            <td>{{ $organiser->code }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>


                    @yield('widgets')
                    @yield('page')
                </div>

            </div>
        </div>

    </div> <!-- /container -->
    @include('agent.partials.tabbar')
@endsection
