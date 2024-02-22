@extends('layouts.organiser')

<?php $maps = true ?>

@push('page_css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/fullcalendar/fullcalendar.min.css') }}">
@endpush

@section('page')
    @permission('view-dashboard')
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
    @endpermission

    
    <div class="row">
    @permission('view-events')
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
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
                            @forelse($events->sortByDesc('events.start_date')->take(5)->all() as $event)
                                <tr>
                                    <td>{{ $event->title }}</td>
                                    <td>{{$event->location}}</td>
                                    <td>{{$event->start}}</td>
                                    <td>{{$event->end}}</td>
                                    <td><a href="{{ route('organiser.events.view', [$organiser->slug, $event->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a> </td>
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
                </div>
            </div>
        </div>
        @endpermission
        @permission('view-orders')
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Recent Orders
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-advance dt-responsive">
                            <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Event</th>
                                <th>User</th>
                                <th>Order Date</th>
                                <th class="ammount">Amount Paid</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->ref_no }}</td>
                                    <td>{{$order->event}}</td>
                                    <td>{{ $order->user }}</td>
                                    <td>{{$order->order_date}}</td>
                                    <td class="ammount">{{$order->amount ? money($order->amount): 'FREE'}}</td>
                                    <td>{{$order->status}}</td>
                                    <td><a href="{{ route('organiser.orders.view', [$organiser->slug, $order->event_id, $order->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a> </td>
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
        </div>
        @endpermission
    </div>


@endsection

@push('page_js')
<script type="text/javascript" src="{{ asset('plugins/fullcalendar/fullcalendar.min.js') }}"></script>
@endpush

@push('page_scripts')
<script>
    $(function () {
        $('#my_cal').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            defaultDate: moment(),
            navLinks: true, // can click day/week names to navigate views
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: App.MyEvents
        });
    })
</script>
@endpush
