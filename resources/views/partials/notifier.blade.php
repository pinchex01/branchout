@if (count($errors) > 0)

    <div class="alert alert-danger icon-alert clearfix">
        <span class="fa fa-times alert-icon"></span>
        <div class="alert-body">
            <h4 class="alert-title">Oopsy!</h4>
            @foreach ($errors->all() as $error)
                <p class="alert-text p-0 m-0">{{ $error }}</p>
            @endforeach
        </div>
    </div>
    <div class="clearfix"></div>

@endif

@if(Session::has('alerts'))
    @foreach(Session::get('alerts') as $alert)
        <div class="alert icon-alert clearfix alert-{{ $alert['type'] }} alert-dismissible">
            <span class="fa fa-info alert-icon"></span>
            <div class="alert-body">
                {!! $alert['message'] !!}
            </div>
        </div>
        <div class="clearfix"></div>
    @endforeach
@endif

