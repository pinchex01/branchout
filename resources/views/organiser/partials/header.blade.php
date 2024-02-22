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
            <a class="navbar-brand nav-title" href="{{ route('organiser.dashboard',[$organiser->slug]) }}">
                Organiser
            </a>
        </div>
        <div id="navbar-secondary" class="navbar-collapse collapse">
            <ul class="nav navbar-nav tp-icon">
                <li class="{{menu_current_route(['organiser.events*'])}}">
                    <a href="{{ route('organiser.events.index',[$organiser->slug]) }}"><i class="fa fa-calendar"></i> My  Events</a>
                </li>
                <li class="{{menu_current_route(['organiser.bank-accounts*'])}}">
                    <a href="{{ route('organiser.bank-accounts.index',[$organiser->slug]) }}"><i class="fa fa-bank"></i> Payments</a>
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