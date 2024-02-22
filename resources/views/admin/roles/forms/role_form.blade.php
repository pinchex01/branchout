<?php $form_control_options = isset($disabled) && $disabled ? ['class' => 'form-control disable', 'disabled' => 'disabled'] : ['class' => 'form-control'] ?>
<div class="form-group {{$errors->has('display_name')? 'has-error': ''}}">
    {!! Form::label('display_name','Role Name:') !!}
    {!! Form::text('display_name',null,$form_control_options+ ['required'=>'required']) !!}
    {!! $errors->first('display_name', '<span class="help-block">:message</span>') !!}
</div>
<div class="form-group {{$errors->has('description')? 'has-error': ''}}">
    {!! Form::label('description','Role Description') !!}
    {!! Form::textarea('description',null,$form_control_options+ ['required'=>'required']) !!}
    {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
</div>
<div class="form-group {{$errors->has('dashboard')? 'has-error': ''}}">
    {!! Form::label('dashboard','Dashboard') !!}
    {!! Form::select('dashboard',[
        "" => "Select Dashboard",
        'main' => "Main Dashboard",
        "tasks" => "Tasks Dashboard",
        "tickets" => "Service Requests Dashboard",
        "billing" => "Billing Dashboard"
    ],null,['class'=>'form-control' ]) !!}
    {!! $errors->first('dashboard', '<span class="help-block">:message</span>') !!}
</div>
<div class="form-group">
    {!! Form::label('permissions', 'Permissions', ['class' => ' control-label']) !!}
    <select name="permissions[]" class="form-control multiselect" required multiple="multiple">
        @foreach($permissions as $permission)
            <option value="{{ $permission->id }}" @if(isset($role) && in_array($permission->id,$role->permission_ids)) selected @elseif(in_array($permission->id, old('permissions',[]))) selected @endif>
                {{ $permission->display_name}}
            </option>
        @endforeach
    </select>

    @foreach ($errors->get('permissions') as $error)
        <span class="help-block">{{ $error }}</span>
    @endforeach
</div>