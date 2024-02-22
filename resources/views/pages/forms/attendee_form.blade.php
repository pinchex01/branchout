<div class="panel panel-info mt-30">
    <div class="panel-heading">{{ $ticket_name }}: {{ "Ticket Holder {$ticket_count} Details" }}</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{$errors->has("attendees.{$order_count}.first_name")? 'has-error': ''}}">
                    <label for="" class="control-label">First Name <span class="required">*</span> </label>
                    {!! Form::text("attendees[{$order_count}][first_name]",null,['class' => 'form-control txo_fn','required'=>'required'])  !!}
                    {!! $errors->first("attendees.{$order_count}.first_name", '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{$errors->has("attendees.{$order_count}.last_name")? 'has-error': ''}}">
                    <label for="" class="control-label">Last Name <span class="required">*</span> </label>
                    {!! Form::text("attendees[{$order_count}][last_name]",null,['class' => 'form-control txo_ln','required'=>'required'])  !!}
                    {!! $errors->first("attendees.{$order_count}.last_name", '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
        <div class="form-group {{$errors->has("attendees.{$order_count}.email")? 'has-error': ''}}">
            <label for="" class="control-label">Email <span class="required">*</span> </label>
            {!! Form::email("attendees[{$order_count}][email]",null,['class' => 'form-control txo_e','required'=>'required'])  !!}
            {!! $errors->first("attendees.{$order_count}.email", '<span class="help-block">:message</span>') !!}
        </div>
        <div class="form-group {{$errors->has("attendees.{$order_count}.phone.")? 'has-error': ''}}">
            <label for="" class="control-label">Phone <span class="required">*</span> </label>
            {!! Form::text('attendees['.$order_count.'][phone]',null,['class' => 'form-control txo_p','required'=>'required'])  !!}
            {!! $errors->first("attendees.{$order_count}.phone", '<span class="help-block">:message</span>') !!}
        </div>
        <input type="hidden" name="attendees[{{$order_count}}][ticket_id]" value="{{ $ticket_id }}">
    </div>
</div>