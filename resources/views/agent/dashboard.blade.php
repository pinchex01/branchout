@extends('layouts.agent')

<?php $maps = true ?>

@push('page_css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/fullcalendar/fullcalendar.min.css') }}">
@endpush

@section('page')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-success" data-step="1" data-intro="{{ trans('intro.user.total_paid') }}" data-position='right'>
                <div class="card-icon bg-success text-center">
                    <i class="fa fa-calendar-plus-o"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{number_format($total_events)}}</div>
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
                    <div class="h5">{{number_format($sales_info->tickets)}}</div>
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
                    <div class="h5">{{ money($sales_info->commission) }}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Commission Earned</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Subscribed Events
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
                            @forelse($events->sortByDesc('events.start_date')->take(5)->all() as $event)
                                <tr>
                                    <td>{{ $event->name }}</td>
                                    <td>{{$event->location}}</td>
                                    <td>{{$event->start_date->format("Y-m-d")}}</td>
                                    <td>{!! $event->status !!} </td>
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
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Upcoming Events
                </div>
                <div class="panel-body">

                  @forelse($upcoming_events as $event)
                      <figure class="snip1527">
                        <div class="image"><img src="{{ $event->getAvatar() }}" alt="pr-sample23" /></div>
                        <figcaption>
                          <div class="date"><span class="day">{{ $event->start_date->format('d')}}</span><span class="month">{{ $event->start_date->format('M')}}</span></div>
                          <h3>{{ $event->name }}</h3>
                          <p class="pb-10">
                            {{ $event->location}}
                          </p>
                          <form method="POST"
                                action="{{ route('agent.events.become_agent', [$organiser->slug, $event->id]) }}">
                                {!! csrf_field() !!}
                            <button class="btn btn-primary" data-toggle="modal" data-target="#md_evn_agent_{{$event->id}}"><i class="fa fa-plus"></i> Subscribe</button>
                          </form>
                        </figcaption>
                      </figure>
                  @empty
                      <h3>No live events at the moment</h3>
                  @endforelse
                </div>
            </div>
        </div>

    </div>

@endsection
