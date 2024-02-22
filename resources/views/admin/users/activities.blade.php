@extends('layouts.admin')

@section('page')

    <div class="row">
        <div class="col-md-12">
            <!-- END Basic Information PORTLET-->
            <div class="panel with-nav-tabs panel-default">
                <div class="panel-heading panel-n-tab-heading hidden-xs">
                    <h4 class="panel-title pull-left">User Activity</h4>
                    <div class="pull-right">

                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-heading">
                    @include('admin.users._user_details_menu')
                </div>
                <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
                    <h4 class="panel-title pull-left">Activity</h4>
                    <div class="pull-right">

                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body table-settings">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover dt-responsive">
                            <thead>
                            <tr>
                                <th>Description</th>
                                <th>Timestamp</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        {!! $activity->description !!}
                                    </td>
                                    <td>{!! $activity->created_at !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! $activities->render() !!}
                    </div>


                </div>

            </div>

        </div>
    </div>

@stop
