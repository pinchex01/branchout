<div class="row">
    @foreach($events as $event)
        <div class="col-md-6 ">
            <div class="thumbnail brdr bgc-fff pad-10 box-shad btm-mrg-20 property-listing">
                <a href="{{ route('agent.events.view', [$organiser->slug, $event->id]) }}">
                <img alt="100%x200" style="height: 200px; width: 100%; display: block;" src="{{ $event->getAvatar() }}" data-holder-rendered="true">
                </a>
                <div class="caption">
                    <h3><a href="{{ route('agent.events.view', [$organiser->slug, $event->id]) }}">{{ $event->name }}</a> </h3>
                    <p>{!! str_limit($event->description) !!}</p>
                    <span class="fnt-smaller fnt-lighter fnt-arial">
                                   {{ $event->organiser->name }}
                                </span>
                </div>
            </div>

        </div><!-- End Listing-->

    @endforeach
</div>