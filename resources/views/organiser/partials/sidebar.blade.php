@if (!isset($settings))
    <div class="nav-sidebar hidden-xs">
        <div class="profile-card">
            <div class="thumbnail thumbnail-reveal">
                <img class="img-responsive hidden-xs" src="{{$organiser->getAvatar()}}">
                <div class="reveal-holder"><a class="btn btn-default" href="#avatar-modal" data-toggle="modal" role="button"><i class="fa fa-camera"></i> Change Photo</a></div>
            </div>
            <div class="profile-info">
                <!-- <h1 class="profile-des">Tenant</h1>-->
                <h4>{{ title_case(strtolower($organiser->name))}}</h4>
                <ul class="user-profile-info-list">
                    <li class="user-profile-info-list-item">Phone. {{$organiser->phone}}</li>
                    <li class="user-profile-info-list-item">{{$organiser->email}}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="nav-sidebar mt-30">
        <ul class="nav nav-sidebar-nav nav-stacked">
            @isset($event_menu)
                <li class="heading">Event Menu </li>
                <li class="{{menu_current_route('organiser.events.view')}}">
                    <a href="{{ route('organiser.events.view',[$organiser->slug, $event->id]) }}"><i class="fa fa-dashboard"></i> Event Dashboard</a>
                </li>
                <li class="{{menu_current_route('organiser.tickets.*')}}">
                    <a href="{{ route('organiser.tickets.index',[$organiser->slug, $event->id]) }}"><i class="fa fa-ticket"></i> Ticket Types</a>
                </li>
                <li class="{{menu_current_route('organiser.orders.*')}}">
                    <a href="{{ route('organiser.orders.index',[$organiser->slug, $event->id]) }}"><i class="fa fa-print"></i> Orders</a>
                </li>
                <li class="{{menu_current_route('organiser.attendees.*')}}">
                    <a href="{{ route('organiser.attendees.index',[$organiser->slug, $event->id]) }}"><i class="fa fa-users"></i> Guests</a>
                </li>
                <li class="{{menu_current_route('organiser.sales.*')}}">
                    <a href="{{ route('organiser.sales.index',[$organiser->slug, $event->id]) }}"><i class="fa fa-users"></i> Sales Agents</a>
                </li>
            @endisset
        </ul>
    </div>
@else

@endif
