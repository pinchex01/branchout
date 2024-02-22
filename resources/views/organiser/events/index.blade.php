@extends('layouts.organiser')

<?php $vue = true ?>
<?php $maps = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Manage Events
            <div class="pull-right">
                <a href="{{ route('organiser.events.new', [$organiser->slug]) }}" class="btn btn-primary btn-sm" ><i class="fa fa-calendar-plus-o"></i> Add Event </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-advance dt-responsive">
                    <thead>
                    <tr>
                        <th width="30%">Event</th>
                        <th>Sales Volume</th>
                        <th>Tickets Sold</th>
                        <th>
                          Status
                        </th>
                        <th width="15%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>
                                {{$event->name}} <br />
                                <small class="text-muted"> {{ $event->location}}</small>
                            </td>
                            <td>{{ money($event->sales_volume, '')}}</td>
                            <td>{{$event->tickets_sold }}</td>
                            <td>{!! $event->status_label  !!}</td>
                            <td>
                                <a href="{{ route('organiser.events.view',[$organiser->slug, $event->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a>
                                <a href="{{ route('organiser.events.edit',[$organiser->slug, $event->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <p class="text-center">
                                    No event added yet. <br>
                                    <a href="{{ route('organiser.events.new', [$organiser->slug]) }}" class="btn btn-primary btn-sm" ><i class="fa fa-calendar-plus-o"></i> Add Event </a>
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
