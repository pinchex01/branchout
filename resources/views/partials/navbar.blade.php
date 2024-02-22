@if(user())
<aside id="sidebar" class="sidebar sidebar-default sidenav visible-xs" role="navigation">
    <!-- Sidebar header -->
    <div class="sidebar-header header-cover">

        <!-- Sidebar toggle button -->
        <button type="button" class="sidebar-toggle" onclick="closeNav()">
            <i class="fa fa-arrow-left"></i>
        </button>
        <!-- Sidebar brand image -->
        <div class="sidebar-image">
            <img src="{{ user()->getAvatar()}}">
        </div>
        <!-- Sidebar brand name -->
        <a data-toggle="dropdown" class="sidebar-brand" href="#settings-dropdown">
            {{ user()->full_name }}
            <b class="caret"></b>
        </a>
    </div>

    <!-- Sidebar navigation -->
    <ul class="nav sidebar-nav">
        <li class="dropdown">
            <ul id="settings-dropdown" class="dropdown-menu">
                <li>
                    <a href="#" tabindex="-1">
                        Profile
                    </a>
                </li>
                <li>
                    <a href="#" tabindex="-1">
                        Settings
                    </a>
                </li>
                <li>
                    <a href="#" tabindex="-1">
                        Help
                    </a>
                </li>
                <li>
                    <a href="#" tabindex="-1">
                        Exit
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ route('account.dashboard')}}">
                <i class="sidebar-icon material-icons">home</i>
                Home
            </a>
        </li>
        <li>
            <a href="{{ route('app.orders.list')}}">
                <i class="sidebar-icon material-icons">shopping_cart</i>
                My Orders
            </a>
        </li>
        <li>
            <a href="{{ route('app.tickets.index')}}">
                <i class="sidebar-icon material-icons">receipt</i>
                My Tickets
            </a>
        </li>

        @if(user()->organisers->where('type','sales-agent')->count())
          <li>
              <a href="{{ route('account.organisers.new-sales-agent') }}">
                  <i class="sidebar-icon material-icons">verified_user</i>
                  Agent
              </a>
          </li>

        @else
          <li class="">
              <a href="{{ route('account.organisers.new-sales-agent') }}"><i class="sidebar-icon fa fa-user-secret"></i> Become an Agent</a>
          </li>
        @endif
        @if(user()->organisers->where('type','organiser')->count())
          <li>
              <a href="{{ route('account.organisers.index') }}">
                  <i class="sidebar-icon fa fa-user-md"></i>
                  Organiser
              </a>
          </li>
        @else
          <li>
              <a href="{{ route('account.organisers.index') }}">
                  <i class="sidebar-icon fa fa-user-md"></i>
                  Become an Organiser
              </a>
          </li>
        @endif
        @if(current_route_is('organiser.*'))
        <li class="divider"></li>
        <li class="">
            <a href="{{ route('organiser.dashboard', [$organiser->slug])}}">
                Orgnaniser Menu <small class="text-muted">{{ $organiser->name }}</small>
            </a>
        </li>
        <li class="divider"></li>
        <li class="{{menu_current_route(['organiser.events.index'])}}">
            <a href="{{ route('organiser.events.index',[$organiser->slug]) }}"><i class="sidebar-icon fa fa-calendar"></i> Manage Events</a>
        </li>
        <li class="{{menu_current_route(['organiser.bank-accounts*'])}}">
            <a href="{{ route('organiser.bank-accounts.index',[$organiser->slug]) }}"><i class="sidebar-icon fa fa-bank"></i> Bank Accounts</a>
        </li>
        @isset($event_menu)
            <li class="heading">Event Menu </li>
            <li class="{{menu_current_route('organiser.events.view')}}">
                <a href="{{ route('organiser.events.view',[$organiser->slug, $event->id]) }}"><i class="sidebar-icon fa fa-dashboard"></i> Event Dashboard</a>
            </li>
            <li class="{{menu_current_route('organiser.tickets.*')}}">
                <a href="{{ route('organiser.tickets.index',[$organiser->slug, $event->id]) }}"><i class="sidebar-icon fa fa-ticket"></i> Ticket Types</a>
            </li>
            <li class="{{menu_current_route('organiser.orders.*')}}">
                <a href="{{ route('organiser.orders.index',[$organiser->slug, $event->id]) }}"><i class="sidebar-icon fa fa-print"></i> Orders</a>
            </li>
            <li class="{{menu_current_route('organiser.attendees.*')}}">
                <a href="{{ route('organiser.attendees.index',[$organiser->slug, $event->id]) }}"><i class="sidebar-icon fa fa-users"></i> Guests</a>
            </li>
            <li class="{{menu_current_route('organiser.sales.*')}}">
                <a href="{{ route('organiser.sales.index',[$organiser->slug, $event->id]) }}"><i class="sidebar-icon fa fa-users"></i> Sales Agents</a>
            </li>
        @endisset
        @endif

        @if(current_route_is('agent.*'))
        <li class="divider"></li>
        <li>
            <a href="{{ route('agent.dashboard', [$organiser->slug])}}">
                Agent Menu <small class="text-muted">{{ $organiser->name }}</small>
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href=" {{ route('agent.events.index', [$organiser->slug])}}">
                Subscribed Events
                <span class="sidebar-badge">3</span>
            </a>
        </li>
        <li>
            <a href=" {{ route('agent.orders.index', [$organiser->slug])}}">
                Agent Sales
                <span class="sidebar-badge">456</span>
            </a>
        </li>
        <li>
            <a href=" {{ route('agent.bank-accounts.index', [$organiser->slug] )}}">
                My Money

            </a>
        </li>
        @endif
        <li class="divider"></li>
        <li>
            <a href="{{ route('auth.logout')}}">
                Sign out
            </a>
        </li>

    </ul>
    <!-- Sidebar divider -->
    <!-- <div class="sidebar-divider"></div> -->

    <!-- Sidebar text -->
    <!--  <div class="sidebar-text">Text</div> -->
</aside>
@endif
<nav class="navbar navbar-inverse navbar-dark navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            @if(!user())
                <a href="{{ route('auth.login') }}" type="button" class="button navbar-toggle collapsed" style="color: #fff;"
                  aria-expanded="false" aria-controls="navbar">
                Sign In
            </a>
            @endif
            <a href="#" type="button" class="button navbar-toggle collapsed" style="color: #fff;"
                    aria-expanded="false" aria-controls="navbar" onclick="openNav()">
                    <i class="fa fa-bars"></i>
                </a>
            <a class="navbar-brand-img" href="/"><img
                        src="{{asset('images/logo-orange.png')}}" class="logo-img"></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse hidden-xs">
            @if(user())
                <ul class="nav navbar-nav">
                    <li class="hidden-sm hidden-md hidden-lg">
                        <a href="#">Settings</a>
                    </li>
                    <li class="hidden-sm hidden-md hidden-lg"><a href="{{route('auth.logout')}}">Logout</a>

                </ul>
            @endif

            <ul class="nav navbar-nav navbar-right">
                @if(user())
                    @if(!user()->organisers->where('type','sales-agent')->count())
                        <li class="hidden-xs">
                            <a href="{{ route('account.organisers.new-sales-agent') }}">Become an Agent</a>
                        </li>
                    @endif
                    @if(!user()->organisers->where('type','organiser')->count())
                        <li class="hidden-xs">
                            <a href="{{ route('account.organisers.index') }}">Become an Organiser</a>
                        </li>
                    @endif
                    <li class="hidden-xs">
                        <a href="{{ route('account.dashboard') }}">{{ title_case(strtolower(user()->full_name))}}</a>
                    </li>
                    <li class="hidden-xs">
                        <a href="{{route('auth.logout')}}"><i class="fa fa-sign-out"></i> Logout</a>
                    </li>
                @else
                    <li class="" style="z-index: 1000;">
                        <a href="{{ route('auth.login') }}" class="button " style="color: #DDD;">Sign In</a>
                    </li>
                    <li class="" style="z-index: 1000;">
                        <a href="{{ route('auth.register') }}" class="button btn-signin navbar-btn">Sign Up</a>
                    </li>
                @endif

            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>
