<div class="container mt-50 visible-xs">
    <div class="row">
        <div class="col-md-12">
            <div class="navbar navbar-default navbar-fixed-bottom">
              <ul class="nav nav-tabs nav-justified" id="app-navbar">
                <li role="presentation" class="{{menu_current_route(['organiser.dashboard'])}}">
                  <a href="{{ route('organiser.dashboard', [$organiser->slug])}}"> <i class="material-icons">dashboard</i><br>Home</a>
                </li>
                <li role="presentation" class="{{menu_current_route(['organiser.events.*'])}}">
                  <a href="{{ route('organiser.events.index', [$organiser->slug])  }}"> <i class="material-icons">date_range</i> <br> Events</a>
                </li>
                <li role="presentation" class="{{menu_current_route(['organiser.orders.*'])}}">
                  <a href="#"> <i class="material-icons">shopping_cart</i> <br> Orders</a>
                </li>
                <li role="presentation" class="{{menu_current_route(['organiser.bank-accounts.*'])}}">
                  <a href="{{ route('organiser.bank-accounts.index', [$organiser->slug])  }}"> <i class="material-icons">attach_money</i> <br> My Money</a>
                </li>
              </ul

            </div>
        </div>
    </div>
</div>
