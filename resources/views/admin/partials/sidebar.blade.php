@if (!isset($layout))
    <div class="nav-sidebar hidden-xs">

        <div class="profile-card">
            <div class="thumbnail thumbnail-reveal">
                <img class="img-responsive hidden-xs" src="{{user()->getAvatar()}}">
                <div class="reveal-holder"><a class="btn btn-default" href="#avatar-modal" data-toggle="modal" role="button"><i class="fa fa-camera"></i> Change Photo</a></div>
            </div>
            <div class="profile-info">

                <h4>{{ title_case(strtolower(user()->full_name))}}</h4>
                <ul class="user-profile-info-list">
                    <li class="user-profile-info-list-item">ID No. {{user()->id_number}}</li>
                    <li class="user-profile-info-list-item">Phone. {{user()->phone}}</li>
                    <li class="user-profile-info-list-item">{{user()->email}}</li>
                </ul>
            </div>
        </div>

        <ul class="nav nav-sidebar-nav nav-stacked mt-10">
            <li class="heading">Quick Links</li>
            @permission(['view-applications', 'approve-applications','review-applications'])
            <li class="{{menu_current_route('admin.tasks*')}}"><a href="{{route('admin.tasks.queue')}}"> Tasks</a></li>
            @endpermission
            @permission(['view-banks'])
            <li class="{{menu_current_route('admin.bank-accounts.*')}}"><a href="{{route('admin.bank-accounts.index')}}"> Manage Banks</a></li>
            @endpermission
            @permission(['view-merchants'])
            <li class="{{menu_current_route('admin.organisers.*')}}"><a href="{{route('admin.organisers.index')}}"> Manage Organisers</a></li>
            @endpermission
            @permission(['view-users'])
            <li class="{{menu_current_route('admin.users.*')}}"><a href="{{route('admin.users.index')}}"> Manage Users</a></li>
            @endpermission

            @isset($event_menu)
                <li class="heading">Event Menu </li>
                <li class="{{menu_current_route('admin.events.view')}}">
                    <a href="{{ route('admin.events.view',[$event->id]) }}"><i class="fa fa-dashboard"></i> Event Dashboard</a>
                </li>
                <li class="{{menu_current_route('admin.tickets.*')}}">
                    <a href="#"><i class="fa fa-ticket"></i> Tickets</a>
                </li>
                <li class="{{menu_current_route('admin.orders.*')}}">
                    <a href="#"><i class="fa fa-print"></i> Orders</a>
                </li>
                <li class="{{menu_current_route('admin.attendees.*')}}">
                    <a href="#"><i class="fa fa-users"></i> Attendees</a>
                </li>
            @endisset

        </ul>
    </div>
@elseif($layout == 'settings')

@endif
