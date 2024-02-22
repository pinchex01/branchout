@extends('admin.organisers.layouts.organiser_details')

@section('organiser_info')
    <div class="table-responsive">
        <table class="table table-striped table-hover table-advance dt-responsive">
            <thead>
            <tr>
                <th>Event</th>
                <th>Location</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($events as $event)
                <tr>
                    <td>{{ $event->name }}</td>
                    <td>{{$event->location}}</td>
                    <td>{{$event->start_date}}</td>
                    <td>{{$event->end_date}}</td>
                    <td><a href="{{ route('admin.events.view', [$event->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a> </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <p class="text-center">
                            No live events at the moment <br>
                        </p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {!! $events->render() !!}
@stop


