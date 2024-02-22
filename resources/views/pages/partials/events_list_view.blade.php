<?php $agent_url = request()->get('agent') ? ['agent'=> request()->get('agent')] : []; ?>
@foreach($events as $event)
    <div class="artist-event-item">
        <div class="row">
            <div class="artist-event-item-info col-sm-9">
                <h3>{{ $event->name }}</h3>
                <ul class="row">
                    <li class="col-sm-5">
                        <span>Venue</span>
                        <span class="location">{{ $event->location }}</span>
                    </li>
                    <li class="col-sm-4">
                        <span>{{ $event->start_date->format('l') }}</span>
                        {{ $event->start_date->format("F. jS, Y") }}
                    </li>
                    <li class="col-sm-3">
                        <span>Time</span>
                        {{ $event->start_date->format("h:i:s A") }}
                    </li>
                </ul>
            </div>
            <div class="artist-event-item-price col-sm-3">
                <span>Price From</span>
                <strong>{{ money($event->tickets->min('price')) }}</strong>
                <a href="{{ route('app.events.view', [$event->slug ] + $agent_url) }}"> Buy Ticket</a>
            </div>
        </div>
    </div>
@endforeach