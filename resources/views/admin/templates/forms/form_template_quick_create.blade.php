<div class="panel">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="panel-title">Add Template</h4>
        </div>
        {!! csrf_field() !!}
        <div class="panel-body">
            <div class="form-group {{$errors->has('name')? 'has-error': ''}}">
                {!! Form::label('name','Template Name:') !!}
                {!! Form::text('name',null,['class'=>'form-control','placeholder'=>'Template Name']) !!}
                {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
            </div>

            <div class="form-group {{$errors->has('event')? 'has-error': ''}}">
                {!! Form::label('event','Event:') !!}
                {!! Form::select('event',$events,null,['class'=>'form-control']) !!}
                {!! $errors->first('event', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i>
                Submit
            </button>
            <button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i
                        class="fa fa-times-circle-o"></i> Cancel
            </button>
        </div>
</div>