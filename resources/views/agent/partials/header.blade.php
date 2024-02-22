<nav class="navbar navbar-default navbar-fixed-top navbar-secondary">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-secondary"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand nav-title" href="{{ route('agent.dashboard',[$organiser->slug]) }}">
                Agent
            </a>
        </div>
        <div id="navbar-secondary" class="navbar-collapse collapse">
            <ul class="nav navbar-nav tp-icon">
                <li class="{{menu_current_route(['agent.events.browse'])}}">
                    <a href="{{ route('agent.events.browse',[$organiser->slug]) }}"><i class="fa fa-search"></i> Find Events</a>
                </li>
                <li class="{{menu_current_route(['agent.events.index'])}}">
                    <a href="{{ route('agent.events.index',[$organiser->slug]) }}"><i class="fa fa-calendar"></i> My Events</a>
                </li>
                <li class="{{menu_current_route(['agent.orders.*'])}}">
                    <a href="{{ route('agent.orders.index',[$organiser->slug]) }}"><i class="fa fa-shopping-cart"></i> Sales </a>
                </li>
                <li class="{{menu_current_route(['agent.bank-accounts*'])}}">
                    <a href="{{ route('agent.bank-accounts.index',[$organiser->slug]) }}"><i class="fa fa-bank"></i> My Money </a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right hidden-xs">
                <li class="#">
                    <a href="#"><i class="fa fa-cogs"></i> Settings</a>
                </li>

            </ul>

        </div>
        <!--/.nav-collapse -->
    </div>
</nav>