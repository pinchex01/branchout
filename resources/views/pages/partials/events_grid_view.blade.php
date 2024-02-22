<?php $agent_url = request()->get('agent') ? ['agent'=> request()->get('agent')] : []; ?>
<section class="section-calendar-events">
    <div class="container">
        <div class="row">
            <div class="section-content">
                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="tab1">
                        <ul class="clearfix">

                                @foreach($events as $event)
                                <li>
                                    <div class="date">
                                        <div href="{{ route('app.events.view', [$event->slug ] + $agent_url) }}">
                                            <span class="day">{{ $event->start_date->format('d') }}</span>
                                            <span class="month">{{ $event->start_date->format('M') }}</span>
                                            <span class="year">{{ $event->start_date->format('Y') }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('app.events.view', [$event->slug ] + $agent_url) }}">
                                        <img src="{{ $event->getAvatar() }}" alt="image" style="max-width:100%; max-height:100%;">
                                    </a>
                                    <div class="info pt-10">
                                        <p>{{ $event->name }}<span> {{ $event->location }}</span></p>
                                        <a href="{{ route('app.events.view', [$event->slug ] + $agent_url) }}" class="get-ticket">Get Ticket</a>
                                    </div>
                                </li>

                                @endforeach

                        </ul>

                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
