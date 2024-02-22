

<?php $form_control_options = isset($disabled) ? $disabled? ['class' => 'form-control disable','disabled'=>'disabled'] : ['class' => 'form-control'] : ['class' => 'form-control'] ?>

<div class="row">
    <div class="col-md-8">
        <div class="form-group {{$errors->has('name')? 'has-error': ''}}">
            {!! Form::label('name','Template Name:') !!}
            {!! Form::text('name',null,$form_control_options ) !!}
            {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group {{$errors->has('event')? 'has-error': ''}}">
            {!! Form::label('event','Event:') !!}
            {!! Form::select('event',$events,null,$form_control_options ) !!}
            {!! $errors->first('event', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <fieldset>
            <div class="form-group @if($errors->has('email_content'))has-error @endif">
                {!! Form::label('email_content', 'Email Content', ['class' => 'control-label required']) !!}
                <?php $wysiwyg = array_add($form_control_options,'id','wysiwyg') ?>
                {!! Form::textarea('email_content', null, $wysiwyg) !!}
                @foreach ($errors->get('content') as $error)
                    <span class="help-block">{{ $error }}</span>
                @endforeach
            </div>
        </fieldset>
        <fieldset>
            <div class="form-group @if($errors->has('sms_content'))has-error @endif">
                {!! Form::label('sms_content', 'SMS Content', ['class' => 'control-label required']) !!}
                <?php $wysiwyg = array_add($form_control_options,'id','wysiwyg2') ?>
                {!! Form::textarea('sms_content', null, $wysiwyg) !!}
                @foreach ($errors->get('sms_content') as $error)
                    <span class="help-block">{{ $error }}</span>
                @endforeach
            </div>
        </fieldset>
    </div>
</div>



