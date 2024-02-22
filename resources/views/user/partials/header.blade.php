<nav class="navbar navbar-default navbar-fixed-top navbar-secondary hidden-xs">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-secondary"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand nav-title" href="{{ route('account.dashboard') }}">
                <i class="fa fa-dashboard"></i> Dashboard
            </a>
        </div>
        <div id="navbar-secondary" class="navbar-collapse collapse">
            <ul class="nav navbar-nav tp-icon">
                <li class="{{menu_current_route(['app.events.list'])}}">
                    <a href="{{ route('app.events.list')  }}"><i class="fa fa-calendar"></i> Upcoming Events</a>
                </li>
                <li class="{{menu_current_route(['app.orders*'])}}">
                    <a href="{{ route('app.orders.list') }}"><i class="fa fa-ticket"></i> My Orders</a>
                </li>
                <li class="{{menu_current_route(['app.tickets*'])}}">
                    <a href="{{ route('app.tickets.index') }}"><i class="fa fa-ticket"></i> My Tickets</a>
                </li>
                @if(user()->organisers->where('type','sales-agent')->count())
                    <li class="{{menu_current_route(['account.organisers.new-sales-agent'])}}">
                        <a href="{{ route('account.organisers.new-sales-agent') }}"><i class="fa fa-user-secret"></i> Agent</a>
                    </li>
                @endif
                @if(user()->organisers->where('type','organiser')->count())
                    <li class="{{menu_current_route(['account.organisers.index'])}}">
                        <a href="{{ route('account.organisers.index') }}"><i class="fa fa-user-md"></i> Organiser</a>
                    </li>
                @endif
            </ul>
            <ul class="nav navbar-nav navbar-right hidden-xs">
                <li class="active">
                    <a href="#" style="font-size: large !important;"> <strong>  Contact Us : 0739 - 595258  </strong></a>
                </li>

            </ul>

        </div>
        <!--/.nav-collapse -->
    </div>
</nav>
