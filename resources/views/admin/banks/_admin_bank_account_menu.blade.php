<ul class="nav nav-tabs">
    <li class="{{menu_current_route('admin.banks.view','active')}}">
        <a href="{{ route('admin.banks.view', [$bank->id]) }}">
            <i class="fa fa-money"></i> <strong>Settlements</strong></a>
    </li>
    <li class="{{menu_current_route('admin.banks.statements','active')}}">
        <a href="{{ route('admin.banks.statements', [$bank->id]) }}">
            <i class="fa fa-book"></i> <strong>Statements</strong></a>
    </li>
</ul>