<ul class="nav nav-tabs">
    <li class="{{menu_current_route('admin.settings.general')}}">
        <a href="{{route('admin.settings.general')}}"><i class="fa fa-globe"></i> General</a>
    </li>
    <li class="{{menu_current_route('admin.settings.ticket_template')}}">
        <a href="{{route('admin.settings.ticket_template')}}"><i class="fa fa-ticket"></i> Ticket Design</a>
    </li>
    <li class="{{menu_current_route('admin.settings.banks*')}}">
        <a href="{{route('admin.settings.banks.index')}}"><i class="fa fa-bank"></i> Banks</a>
    </li>

    <li class="{{menu_current_route('admin.settings.roles*')}}">
        <a href="#"><i class="fa fa-shield"></i> Groups</a>
    </li>
    <li class="{{menu_current_route('admin.settings.staffs*')}}">
        <a href="{{route('admin.settings.staffs.index')}}"><i class="fa fa-users"></i> Staff</a>
    </li>
    <li class="{{menu_current_route('admin.settings.merchant-roles*')}}">
        <a href="{{ route('admin.settings.merchant-roles.index') }}"><i class="fa fa-shield"></i> Merchant Roles</a>
    </li>
    @permission('view-tariffs')
    <li class="{{menu_current_route('admin.settings.tariffs*')}}">
        <a href="#"><i class="fa fa-sitemap"></i> Tariffs</a>
    </li>
    @endpermission
</ul>
