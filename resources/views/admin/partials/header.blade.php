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
            <a class="navbar-brand nav-title" href="{{ route('admin.dashboard') }}">
                Control Panel
            </a>
        </div>
        <div id="navbar-secondary" class="navbar-collapse collapse">
            <ul class="nav navbar-nav tp-icon">
                @permission(['view-events'])
                <li class="{{menu_current_route(['admin.events.*'])}}">
                    <a href="{{route('admin.events.index')}}"><i class="fa fa-calendar"></i> Events</a>
                </li>
                @endpermission
                @permission(['view-orders'])
                <li class="{{menu_current_route('admin.orders.*')}}">
                    <a href="{{ route('admin.orders.index') }}"><i class="fa fa-shopping-cart"></i> Orders</a>
                </li>
                @endpermission
                @permission(['view-orders'])
                <li class="{{menu_current_route('admin.payments.*')}}">
                    <a href="{{ route('admin.payments.index') }}"><i class="fa fa-money"></i> Payments</a>
                </li>
                @endpermission
                @permission(['view-banks'])
                <li class="{{menu_current_route('admin.bank-accounts.*')}} hidden-lg hidden-md"><a
                            href="{{route('admin.bank-accounts.index')}}"><i class="fa fa-bank"></i> Manage Banks</a></li>
                @endpermission
                @permission(['view-merchants'])
                <li class="{{menu_current_route('admin.organisers.*')}} hidden-lg hidden-md">
                    <a href="{{route('admin.organisers.index')}}"><i class="fa fa-user-md"></i>
                        Manage Organisers</a></li>
                @endpermission
                @permission(['view-users'])
                <li class="{{menu_current_route('admin.users.*')}} hidden-lg hidden-md">
                    <a href="{{route('admin.users.index')}}"> <i class="fa fa-user-times"></i> Manage
                        Users</a></li>
                @endpermission
                @permission(['view-banks'])
                <li class="{{menu_current_route('admin.banks*')}} hidden-lg hidden-md">
                    <a href="{{route('admin.banks.index')}}"><i class="fa fa-dollar"></i> My
                        Money</a></li>
                @endpermission

            </ul>
            <ul class="nav navbar-nav navbar-right hidden-xs">
                <li class="#">
                    <a href="{{ route('admin.settings.general') }}"><i class="fa fa-cogs"></i> Settings</a>
                </li>

            </ul>

        </div>
        <!--/.nav-collapse -->
    </div>
</nav>