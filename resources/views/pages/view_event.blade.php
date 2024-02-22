@extends('layouts.page')

<?php $vue = true ?>
<?php $maps = true ?>

@section('body_class','body-login')
@push('page_css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/flipclock/flipclock.css') }}">
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @include('partials.notifier')
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class=" event-detail">
                            <div class="row">
                                <div class="col-md-12">
                                    <h2 class="main-title">{{ $event->name }}</h2>
                                    
                                    <div class="event-detail-img">
                                        <img src="{{ $event->getAvatar() }}" alt="" height="250">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id class="tab_container event_tabs">
                    <input id="tab1" type="radio" name="tabs" checked>
                    <label for="tab1"><i class="fa fa-shopping-cart"></i><span> Buy Tickets</span></label>

                    <input id="tab2" type="radio" name="tabs" >
                    <label for="tab2"><i class="fa fa-pencil-square-o"></i><span>About</span></label>

                    <input id="tab3" type="radio" name="tabs">
                    <label for="tab3"><i class="fa fa-comments"></i><span> Comments</span></label>

                    <section id="content1" class="tab-content">

                        <buy-event-tickets-form
                                event="{{ $event->id }}"
                                cart_id="{{ session()->getId() }}" @if($organiser) agent_id="{{ $organiser->id }}"  @endif></buy-event-tickets-form>

                    </section>
                    
                    <section id="content2" class="tab-content">
                        <div class="row">
                            <div class="col-md-8">
                                {!! $event->description !!}

                            </div>
                            <div class="col-md-4">
                                <strong>Location: </strong> {{ $event->location}} <br>
                                <strong>Date: </strong> {{ $event->start_date}} <br>
                                <strong>Organiser: </strong> {{ $event->organiser}} <br>
                                <strong>Contact: </strong> {{ $event->organiser->phone}} <br>

                                <div id="evt_m" class="map_canvas map" ></div>
                                
                                
                                <div class="actions">
                                    @if ($organiser)
                                        @if (!$organiser->is_agent($event->id))
                                            <button id="cmd-agent" class="btn btn-primary btn-lg btn-block" data-toggle="modal"
                                                    data-target="#md_evn_agent">
                                                <i class="fa fa-user-secret"></i> Become an agent
                                            </button>

                                            @push('modals')
                                            <div class="modal fade" id="md_evn_agent" tabindex="-1" role="dialog"
                                                 aria-labelledby="exampleModalLabel">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <form class="" method="POST"
                                                              action="{{ route('agent.events.become_agent', [$organiser->slug, $event->id]) }}">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close"><span
                                                                            aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="exampleModalLabel">Become an agent</h4>
                                                            </div>
                                                            <div class="modal-body">

                                                                {!! csrf_field() !!}
                                                                <p class="text-muted">
                                                                    By choosing to become an agent, you will earn a commissions based on
                                                                    volume of tickets you sell.
                                                                    Do you wish to proceed?
                                                                </p>

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                                <button type="submit" class="btn btn-primary"> Yes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endpush
                                        @else
                                            <p class="note note-info mt-10">
                                                You are already an agent for this event
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="content3" class="tab-content">
                        <comment-form ref_id="{{ $event->id }}" ref_type="event"></comment-form>
                        <div id="comments-box">
                            @forelse($comments as $comment)
                                <div class="media" id="comment_item_{{$comment->pk}}">
                                    <div class="media-left">
                                        <a href="#">
                                            <img class="media-object" src="{{ $comment->author->getAvatar() }}" alt="avatar" height="64">
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading"> {{ $comment->author->full_name }}</h4>
                                        {!! $comment->notes !!}
                                    </div>
                                </div>
                            @empty
                                <h4> No comment has been posted yet. Be the first</h4>
                            @endforelse
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

@endsection

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
