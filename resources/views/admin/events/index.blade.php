@extends('layouts.admin')

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
                        <th>Organiser</th>
                        <th>Sales Volume</th>
                        <th>Tickets Sold</th>
                        <th>Status</th>
                        <th width="10%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>
                                {{$event->name}}
                            </td>
                            <td>{{$event->organiser}}</td>
                            <td>{{ money($event->sales_volume, '') }}</td>
                            <td>{{$event->tickets_sold }}</td>
                            <td>{!! $event->status_label !!}</td>
                            <td>
                                <a href="{{ route('admin.events.view',[$event->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <p class="text-center">
                                    No events added yet.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {!! $events->render() !!}
            </div>
        </div>
    </div>
@endsection
