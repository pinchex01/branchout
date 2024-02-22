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
    @isset($public_events)
        <div class="nav-sidebar mt-30">
            <form>
                <div class="panel panel-default">
                    <div class="panel-body">
                        @include('pages.forms.events_frontend_search_form')
                        <div class="form-group">
                            <button type="submit" class="btn btn-danger btn-block"><i class="fa fa-filter"></i> Search </button>
                            <a href="#" class="btn btn-default btn-block"><i class="fa fa-times"></i> Reset </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endisset
    <div class="nav-sidebar mt-30">
        <ul class="nav nav-sidebar-nav nav-stacked">
            @isset($event_menu)
                <li class="heading">Event Menu </li>
                <li class="{{menu_current_route('agent.events.view')}}">
                    <a href="{{ route('agent.events.view',[$organiser->slug, $event->id]) }}"><i class="fa fa-dashboard"></i> Event Dashboard</a>
                </li>
                <li class="{{menu_current_route('agent.orders.*')}}">
                    <a href="{{ route('agent.orders.index',[$organiser->slug, $event->id]) }}"><i class="fa fa-print"></i> Orders</a>
                </li>

            @endisset
        </ul>
    </div>
@else

@endif
