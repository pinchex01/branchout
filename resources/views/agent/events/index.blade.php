@extends('layouts.agent')

<?php $vue = true ?>
<?php $maps = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Manage Events
            <div class="pull-right">

            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-advance dt-responsive">
                    <thead>
                    <tr>
                        <th>Event</th>
                        <th>Location</th>
                        <th>Start Date</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>
                                {{$event->name}}
                            </td>
                            <td>{{$event->location}}</td>
                            <td>{{ $event->start_date->format('Y-m-d')}}</td>
                            <td>{!! $event->status  !!}</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <p class="text-center">
                                    No event added yet. <br>

                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
