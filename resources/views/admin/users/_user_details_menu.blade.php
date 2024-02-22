<ul class="nav nav-tabs">
    <li class="{{menu_current_route('admin.users.view','active')}}">
        <a href="{{ route('admin.users.view', [$user->id]) }}">
            <i class="fa fa-info-circle"></i> <strong>Details</strong></a>
    </li>

    <li class="{{menu_current_route('admin.users.roles*','active')}}">
        <a href="{{ route('admin.users.roles', [$user->id]) }}">
            <i class="fa fa-users"></i> <strong>Roles</strong></a>
    </li>
    <li class="{{menu_current_route('admin.users.activities','active')}}">
        <a href="{{ route('admin.users.activities', [$user->id]) }}">
            <i class="fa fa-clock-o"></i> <strong>Activity</strong></a>
    </li>
</ul>
