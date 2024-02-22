<ul class="nav nav-tabs pull-left">
    @if(user()->hasRole('super-admin'))
        <li class="{{menu_current_route('admin.tasks.index','active')}}">
            <a href="{{ route('admin.tasks.index') }}">
                <i class="fa fa-list"></i> <strong>All</strong></a>
        </li>
    @endif
    <li class="{{menu_current_route('admin.tasks.queue','active')}}">
        <a href="{{ route('admin.tasks.queue') }}">
            <i class="fa fa-list"></i> <strong>Queued</strong></a>
    </li>
    <li class="{{menu_current_route('admin.tasks.inbox','active')}}">
        <a href="{{ route('admin.tasks.inbox') }}">
            <i class="fa fa-envelope"></i> <strong>My Tasks</strong></a>
    </li>
    <li class="{{menu_current_route('admin.tasks.complete','active')}}">
        <a href="{{ route('admin.tasks.complete') }}">
            <i class="fa fa-check-square"></i> <strong>Completed</strong></a>
    </li>

</ul>
<div class="pull-right">
    @permission(['review-applications','approve-applications'])
    <a href="{{ route('admin.tasks.pick') }}" class="btn btn-primary">
        <i class="fa fa-hand-grab-o"></i> Pick Task
    </a>
    @endpermission
</div>
<div class="clearfix"></div>