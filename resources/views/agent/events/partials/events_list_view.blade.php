@foreach($events as $event)
    <div class="brdr bgc-fff pad-10 box-shad btm-mrg-20 property-listing">
        <div class="media">
            <a class="pull-left" href="{{ route('agent.events.view', [$organiser->slug, $event->id]) }}" target="_parent">
                <img alt="image" class="img-responsive" src="{{$event->getAvatar()}}"></a>

            <div class="clearfix visible-sm"></div>

            <div class="media-body fnt-smaller">
                <a href="#" target="_parent"></a>

                <h4 class="media-heading">
                    <a href="{{ route('agent.events.view', [$organiser->slug, $event->id]) }}" target="_parent">{{ $event->name }} </a></h4>
                <p>
                    <small class="">{{ $event->location }}</small>
                </p>

                <p class="hidden-xs">
                    {!! str_limit($event->description) !!}
                </p>
                <span class="fnt-smaller fnt-lighter fnt-arial">
                                   {{ $event->organiser->name }}
                                </span>
            </div>
        </div>
    </div><!-- End Listing-->

@endforeach