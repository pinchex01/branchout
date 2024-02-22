<?php $form_control_options = isset($disabled) && $disabled ? ['class' => 'form-control disable', 'disabled' => 'disabled'] : ['class' => 'form-control'] ?>
<?php $readonly = array_add($form_control_options,'readonly','readonly') ?>
<fieldset class="row">
    <legend>Basic Information</legend>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{$errors->has('first_name')? 'has-error': ''}}">
                {!! Form::label('first_name','First Name:') !!}
                {!! Form::text('first_name',null,$readonly) !!}
                {!! $errors->first('first_name', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{$errors->has('last_name')? 'has-error': ''}}">
                {!! Form::label('last_name','Last Name:') !!}
                {!! Form::text('last_name',null,$readonly) !!}
                {!! $errors->first('last_name', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{$errors->has('surname')? 'has-error': ''}}">
                {!! Form::label('surname','Surname:') !!}
                {!! Form::text('surname',null,$readonly) !!}
                {!! $errors->first('surname', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{$errors->has('gender')? 'has-error': ''}}">
                {!! Form::label('gender','Gender:') !!}
                {!! Form::select('gender',['Female'=>'Female','Male'=>'Male'],$user->gender,$readonly) !!}
                {!! $errors->first('gender', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{$errors->has('dob')? 'has-error': ''}}">
                {!! Form::label('dob','Date of Birth:') !!}
                {!! Form::text('dob',$user->dob,$readonly) !!}
                {!! $errors->first('dob', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{$errors->has('id_number')? 'has-error': ''}}">
                {!! Form::label('id_number','ID No.:') !!}
                {!! Form::text('id_number',$user->id_number,$readonly) !!}
                {!! $errors->first('id_number', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{$errors->has('citizenship')? 'has-error': ''}}">
                {!! Form::label('citizenship','Nationality:') !!}
                {!! Form::text('citizenship',$user->citizenship,$readonly) !!}
                {!! $errors->first('citizenship', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4">

        </div>
    </div>
</fieldset>

<fieldset class="row">
    <legend>Phone Number</legend>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{$errors->has('phone')? 'has-error': ''}}">
                <label for="phone" class="control-label">Phone No.<span class="required">*</span> </label>
                {!! Form::text('phone',$user->phone,$form_control_options) !!}
                {!! $errors->first('phone', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </div>
</fieldset>
<fieldset class="row">
    <legend>Primary Email Address</legend>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{$errors->has('email')? 'has-error': ''}}">
                <label for="email" class="control-label">Email<span class="required">*</span> </label>
                {!! Form::text('email',$user->email,$form_control_options) !!}
                {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
            </div>
        </div>

    </div>
</fieldset>