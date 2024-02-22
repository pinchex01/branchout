@extends('layouts.admin_settings')

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">
                <span class="caption-subject bold uppercase"> All Settings </span>
            </div>
            <div class="tools pull-right">
                <button class="btn btn-default" type="button" data-toggle="modal" data-target="#md_add_setting"
                        aria-expanded="false" aria-controls="">
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="note note-info mb-20">
                <h4>Welcome, AVENGER</h4>
                With great power comes great responsibility
            </div>
            <div class="table-responsive">
                <table id="t_sets" class="table table-striped table-hover table-advance dt-responsive">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Key</th>
                        <th>Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($settings as $key => $value)
                        <tr>
                            <td>{{$loop->iteration }}</td>
                            <td>{{$key}}</td>
                            <td><a class="st_edit" href="#" id="{{$key}}" data-type="text" data-pk="{{$key}}" data-url="{{route('admin.settings.all.save')}}" data-title="{{$key}}">{{$value}}</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No records found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@push('modals')
<div class="modal fade" id="md_add_setting" tabindex="-1" role="dialog">
    <form class="modal-dialog" role="document" method="post"
          action="{{route('admin.settings.all.add')}}">
        {!! csrf_field() !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Setting</h4>
            </div>
            <div class="modal-body">
                <div class="form-group {{$errors->has('key')? 'has-error': ''}}">
                    <label for="house[name]" class="control-label">Key<span
                                class="required">*</span> </label>
                    {!! Form::text('key',null,['id'=>'key','required'=>'required','placeholder'=>'Key','class'=>'form-control']) !!}
                    {!! $errors->first('key', '<span class="help-block">:message</span>') !!}
                </div>
                <div class="form-group {{$errors->has('value')? 'has-error': ''}}">
                    <label for="house[name]" class="control-label">Value<span
                                class="required">*</span> </label>
                    {!! Form::text('value',null,['id'=>'value','required'=>'required','placeholder'=>'Value','class'=>'form-control']) !!}
                    {!! $errors->first('value', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left"
                        data-dismiss="modal">Close
                </button>
                <button type="submit" class="btn btn-primary">
                     Add </button>
            </div>
        </div><!-- /.modal-content -->
    </form><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endpush


@push('javascripts')
<script>
    $(function() {
        $('.st_edit').editable();
        $('#t_sets').DataTable();
    });
</script>
@endpush