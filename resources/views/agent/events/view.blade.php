@extends('layouts.agent')

<?php $maps = true ?>

@section('body_class','body-login')
@push('page_css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/flipclock/flipclock.css') }}">
@endpush

@section('page')
    <div class="container event-detail">
        <div class="row">
            <div class="col-md-8">
                <h2 class="main-title">{{ $event->name }}</h2>
                <div class="meta clearfix">
                    <span class="date"><i
                                class="icon fa fa-calendar"></i> {{ $event->start_date.' - '. $event->end_date.' @ '.$event->location }}</span>
                    <span><a href="#"><i class="icon fa fa-map-marker"></i> {{ $event->location }}</a></span>
                </div>
                <div class="event-detail-img">
                    <img src="{{ $event->getAvatar() }}" alt="">
                </div>
                <h2 class="title">Whats About</h2>
                <p>
                    {!! $event->description !!}
                </p>

            </div>
            <div class="col-md-4">
                <div id="evt_m" class="map_canvas map" style="margin-top: 100px !important;"></div>
                <div class="actions">
                    <button class="btn btn-info btn-lg btn-block"><i class="fa fa-heart"></i> ADD WISHLIST</button>

                    <button id="cmd-buy-tck" class="btn btn-danger btn-lg btn-block" data-toggle="modal"
                            data-target="#md_tck_b">BUY
                        TICKETS
                    </button>
                    @if (!$organiser->is_agent($event->id))
                        <button id="cmd-agent" class="btn btn-primary btn-lg btn-block" data-toggle="modal"
                                data-target="#md_evn_agent">
                            <i class="fa fa-user-secret"></i> Become an agent
                        </button>
                    @else
                        <p class="note note-info mt-10">
                            You are already an agent for this event
                        </p>
                    @endif

                </div>
            </div>

        </div>
    </div>
@endsection

@push('modals')
<div class="modal fade" id="md_tck_b" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Buy Event Tickets</h4>
            </div>
            <div class="modal-body">
                <form class="" method="POST" action="{{ route('agent.events.buy', [$organiser->slug, $event->id]) }}">
                    {!! csrf_field() !!}
                    <table class="table">
                        <tbody>
                        @forelse($event->tickets as $ticket)
                            <tr>
                                <td><input type="checkbox" name="tickets[{{$ticket->id}}]"></td>
                                <td width="60%">
                                    <h3>{{$ticket->name}}</h3>
                                    {!! $ticket->description !!}
                                </td>
                                <td>
                                    KES {{ $ticket->price }}
                                </td>
                                <td>
                                    <?php $now = \Carbon\Carbon::now() ?>
                                    @if($ticket->on_sale_date->format("Y-m-d") <= $now->format("Y-m-d"))
                                        <div class="form-group {{$errors->has("tickets.{$ticket->id}")? 'has-error': ''}}">
                                            <input id="ticket_id_{{$ticket->id}}" type="number"
                                                   name="quantity[{{$ticket->id}}]" class="form-control ticket_quantity"
                                                   value="{{ old("quantity.{$ticket->id}",0)}}">
                                            {!! $errors->first("tickets.{$ticket->id}", '<span class="help-block">:message</span>') !!}
                                        </div>
                                    @elseif($ticket->end_sale_date->format('Y-m-d') < $now->format('Y-m-d'))
                                        <span class="text-center text-danger">Sales have ended</span>
                                    @else
                                        <span class="text-center text-danger">Sale begins on {{$ticket->on_sale_date->format("Y-m-d")}}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"><h3>This event doesn't have tickets</h3></td>
                            </tr>
                        @endforelse
                        </tbody>
                        @if($event->tickets->count())
                            <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <button type="submit" name="channel" value="pesaflow"
                                            class="btn btn-primary btn-block"> Buy Ticket(s)
                                    </button>
                                </td>
                            </tr>
                            </tfoot>
                        @endif
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="md_evn_agent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="" method="POST"
                  action="{{ route('agent.events.become_agent', [$organiser->slug, $event->id]) }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="exampleModalLabel">Become an agent</h4>
                </div>
                <div class="modal-body">

                    {!! csrf_field() !!}
                    <p class="text-muted">
                        By choosing to become an agent, you will earn a commissions based on volume of tickets you sell.
                        Do you wish to proceed?
                    </p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"> Yes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('page_js')
<script type="text/javascript" src="{{ asset('plugins/jquery.mapit.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/flipclock/flipclock.js') }}"></script>
@endpush

@push('page_scripts')
<script>
    @if ($errors->has("tickets.*"))
        $(function () {
        $("#cmd-buy-tck").trigger('click');
    });
    @endif
$(function () {

        var map = $('#evt_m').mapit({
            latitude: "{{ $event->lat }}",
            longitude: "{{ $event->lng }}",
            zoom: 16,
            type: 'ROADMAP',
            scrollwheel: false,
            marker: {
                latitude: "{{ $event->lat }}",
                longitude: "{{ $event->lng }}",
                icon: '/images/marker_red.png',
                title: '{{ $event->name }}',
                open: false,
                center: true
            },
            address: '<h2>{{ $event->name }}</h2><p>{{ $event->location }}</p>',

            origins: [
                ['37.936294', '23.947394'],
                ['37.975669', '23.733868']
            ]
        });

        var clock = $('#countdown').FlipClock(3600 * 24 * 3, {
            clockFace: 'DailyCounter',
            countdown: true
        });
    })
</script>
@endpush
