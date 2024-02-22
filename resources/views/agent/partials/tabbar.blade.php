<div class="container mt-50 visible-xs">
    <div class="row">
        <div class="col-md-12">
            <div class="navbar navbar-default navbar-fixed-bottom">
              <ul class="nav nav-tabs nav-justified" id="app-navbar">
                <li role="presentation" class="{{menu_current_route(['agent.dashboard'])}}">
                  <a href="{{ route('agent.dashboard', [$organiser->slug])}}"> <i class="material-icons">dashboard</i><br>Home</a>
                </li>
                <li role="presentation" class="{{menu_current_route(['agent.events.*'])}}">
                  <a href="{{ route('agent.events.index', [$organiser->slug])  }}"> <i class="material-icons">date_range</i> <br> Events</a>
                </li>
                <li role="presentation" class="{{menu_current_route(['agent.orders.*'])}}">
                  <a href="{{ route('agent.orders.index', [$organiser->slug])  }}"> <i class="material-icons">shopping_cart</i> <br> My Sales</a>
                </li>
                <li role="presentation" class="{{menu_current_route(['agent.bank-accounts.*'])}}">
                  <a href="{{ route('agent.bank-accounts.index', [$organiser->slug])  }}"> <i class="material-icons">attach_money</i> <br> My Banks</a>
                </li>
              </ul

            </div>
        </div>
    </div>
</div>
