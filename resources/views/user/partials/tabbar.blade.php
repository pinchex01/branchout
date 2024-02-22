<div class="container mt-50 visible-xs">
    <div class="row">
        <div class="col-md-12">
            <div class="navbar navbar-default navbar-fixed-bottom">
              <ul class="nav nav-tabs nav-justified" id="app-navbar">
                <li role="presentation" class="{{menu_current_route(['account.dashboard'])}}">
                  <a href="{{ route('account.dashboard')}}"> <i class="material-icons">home</i><br>Home</a>
                </li>
                <li role="presentation" class="{{menu_current_route(['app.events.*'])}}">
                  <a href="{{ route('app.events.list')  }}"> <i class="material-icons">date_range</i> <br> Events</a>
                </li>
                <li role="presentation" class="{{menu_current_route(['app.orders.*'])}}">
                  <a href="{{ route('app.orders.list')  }}"> <i class="material-icons">shopping_cart</i> <br> My Orders</a>
                </li>
                <li role="presentation" class="{{menu_current_route(['app.tickets.*'])}}">
                  <a href="{{ route('app.tickets.index')  }}"> <i class="material-icons">receipt</i> <br> My Tickets</a>
                </li>
              </ul

            </div>
        </div>
    </div>
</div>
