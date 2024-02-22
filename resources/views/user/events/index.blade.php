@extends('layouts.user')

<?php $vue = true ?>
<?php $maps = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>Events</h3>
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
                        <th>Organiser</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>{{ $event->title }}</td>
                            <td>{{$event->location}}</td>
                            <td>{{$event->start}}</td>
                            <td>{{$event->organiser}}</td>
                            <td><a href="{{ route('app.events.view', $event->slug) }}" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a> </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <p class="text-center">
                                    No orders have been placed yet. <br>
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
