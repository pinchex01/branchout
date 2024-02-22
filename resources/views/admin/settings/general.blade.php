@extends('admin.layouts.settings')

@section('settings')
    <form class="row" method="POST" enctype="multipart/form-data" action="{{route('admin.settings.general')}}">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('site_name', 'Site Title') !!}
                    {!! Form::text('site_name', (settings('site_name')) ? settings('site_name') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('site_url', 'Site Url') !!}
                    {!! Form::text('site_url', (settings('site_url')) ? settings('site_url') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('meta_title', 'Meta Title') !!}
                    {!! Form::text('meta_title', settings('meta_title') ? settings('meta_title') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('site_description', 'Site Description') !!}
                    {!! Form::text('site_description', settings('site_description') ? settings('site_description') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('site_keywords', 'Site Keywords') !!}
                    {!! Form::text('site_keywords', settings('site_keywords') ? settings('site_keywords') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('site_author', 'Site Author') !!}
                    {!! Form::text('site_author', settings('site_author') ? settings('site_author') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    <label>
                        Users can Register?
                        <input type="checkbox" name="available"
                               value="1" {{settings('user_register')? 'checked' : ''}}/>
                    </label>
                </div>
                <div class="form-group">
                    {!! Form::label('date_format', 'Date Format') !!}
                    {!! Form::select('date_format',[
                                'F j Y' => date('F j Y')
                    ], settings('site_author') ? settings('site_author') : null, array('class' => 'form-control')) !!}
                </div>
            </div>
            {!! csrf_field() !!}
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
@stop
