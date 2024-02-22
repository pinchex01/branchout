@if (!isset($settings))
    <div class="nav-sidebar hidden-xs">
        <div class="profile-card">
            <div class="thumbnail thumbnail-reveal">
                <img class="img-responsive hidden-xs" src="{{user()->getAvatar()}}">
                <div class="reveal-holder"><a class="btn btn-default" href="#avatar-modal" data-toggle="modal" role="button"><i class="fa fa-camera"></i> Change Photo</a></div>
            </div>
            <div class="profile-info">
                <!-- <h1 class="profile-des">Tenant</h1>-->
                <h4>{{ title_case(strtolower(user()->full_name))}}</h4>
                <ul class="user-profile-info-list">
                    <li class="user-profile-info-list-item">ID No. {{user()->id_number}}</li>
                    <li class="user-profile-info-list-item">Phone. {{user()->phone}}</li>
                    <li class="user-profile-info-list-item">{{user()->email}}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="nav-sidebar mt-20">
        <ul class="nav nav-sidebar-nav nav-stacked">

            @if(current_route_is('organiser.*'))
                <li class="heading">Organiser Menu</li>
                <li class="{{menu_current_route(['organiser.dashboard'])}}">
                    <a href="{{ route('organiser.dashboard',[$organiser->slug]) }}"><i class="fa fa-dashboard"></i> Dashboard</a>
                </li>
                <li class="{{menu_current_route(['organiser.events.index'])}}">
                    <a href="{{ route('organiser.events.index',[$organiser->slug]) }}"><i class="fa fa-calendar"></i> Manage Events</a>
                </li>
                @permission(['view-banks','create-banks','update-banks','delete-banks'])
                <li class="{{menu_current_route(['organiser.bank-accounts*'])}}">
                    <a href="{{ route('organiser.bank-accounts.index',[$organiser->slug]) }}"><i class="fa fa-bank"></i> Bank Accounts</a>
                </li>
                @endpermission
                @isset($event_menu)
                    <li class="heading">Event Menu </li>
                    <li class="{{menu_current_route('organiser.events.view')}}">
                        <a href="{{ route('organiser.events.view',[$organiser->slug, $event->id]) }}"><i class="fa fa-dashboard"></i> Event Dashboard</a>
                    </li>
                    @permission(['view-tickets','create-tickets','update-tickets','delete-tickets'])
                    <li class="{{menu_current_route('organiser.tickets.*')}}">
                        <a href="{{ route('organiser.tickets.index',[$organiser->slug, $event->id]) }}"><i class="fa fa-ticket"></i> Ticket Types</a>
                    </li>
                    @endpermission
                    @permission(['view-orders','create-orders','update-orders','delete-orders'])
                    <li class="{{menu_current_route('organiser.orders.*')}}">
                        <a href="{{ route('organiser.orders.index',[$organiser->slug, $event->id]) }}"><i class="fa fa-print"></i> Orders</a>
                    </li>
                    @endpermission
                    @permission(['view-attendees','create-attendees','update-attendees'])
                    <li class="{{menu_current_route('organiser.attendees.*')}}">
                        <a href="{{ route('organiser.attendees.index',[$organiser->slug, $event->id]) }}"><i class="fa fa-users"></i> Guests</a>
                    </li>
                    @endpermission
                    @permission(['view-staffs','create-staffs'])
                    <li class="{{menu_current_route('organiser.sales.*')}}">
                        <a href="{{ route('organiser.sales.index',[$organiser->slug, $event->id]) }}"><i class="fa fa-users"></i> Sales Agents</a>
                    </li>
                    @endpermission
                @endisset

            @elseif(current_route_is('agent.*'))
                <li class="heading">Agent Menu</li>
                <li class="{{menu_current_route(['agent.dashboard'])}}">
                    <a href="{{ route('agent.dashboard',[$organiser->slug]) }}"><i class="fa fa-dashboard"></i> Agent Dashboard</a>
                </li>
                <li class="{{menu_current_route(['agent.events.index'])}}">
                    <a href="{{ route('agent.events.index',[$organiser->slug]) }}"><i class="fa fa-calendar"></i> Subscribed Events</a>
                </li>
                <li class="{{menu_current_route(['agent.orders.*'])}}">
                    <a href="{{ route('agent.orders.index',[$organiser->slug]) }}"><i class="fa fa-shopping-cart"></i> Agent Sales </a>
                </li>
                <li class="{{menu_current_route(['agent.bank-accounts*'])}}">
                    <a href="{{ route('agent.bank-accounts.index',[$organiser->slug]) }}"><i class="fa fa-bank"></i> My Money </a>
                </li>
            @else
            @endif

            <li class="heading">
              Organiser
            </li>
              @foreach( user()->organisers->where('type', 'organiser') as $org)
                  <li>
                    <a href="{{ route('organiser.dashboard', [$org->slug]) }}"><i class="fa fa-briefcase"></i> {{ $org->name}}</a>
                  </li>
              @endforeach
              <li>
                <a href="{{ route('account.organisers.index') }}"><i class="sidebar-icon fa fa-plus"></i> Add Organiser</a>
              </li>
              <li class="heading">
                Agents
              </li>
                @foreach( user()->organisers->where('type', 'sales-agent') as $org)
                    <li>
                      <a href="{{ route('agent.dashboard', [$org->slug]) }}"><i class="fa fa-briefcase"></i> {{ $org->name}}</a>
                    </li>
              @endforeach
              <li>
                <a href="{{ route('account.organisers.new-sales-agent') }}"><i class="sidebar-icon fa fa-plus"></i> Add Agent</a>
              </li>
        </ul>
    </div>
@else

@endif
