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

        </div>
        <div id="navbar-secondary" class="navbar-collapse collapse">
            <ul class="nav navbar-nav tp-icon">
                <li class="{{menu_current_route(['app.events.list'])}}">
                    <a href="{{ route('app.events.list') }}"> Upcoming Events</a>
                </li>
                <li class="">
                    <a href="#"> How It Works</a>
                </li>
                <li class="">
                    <a href="#"> FAQs</a>
                </li>
                <li class="">
                    <a href="#"> Testimonies</a>
                </li>
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