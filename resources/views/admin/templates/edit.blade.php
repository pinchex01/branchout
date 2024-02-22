@extends('layouts.admin_settings')


@section('page')

    {!! Form::model($template, ['id' => 'template-form', 'class' => 'form']) !!}
    <div class="panel panel-default">
        <div class="panel-heading">

            <h4 class="panel-title pull-left">Edit Template</h4>
            <div class="tools pull-right">
                <a class="btn btn-default" href="{{route('admin.settings.templates.preview',[$template->id])}}" target="_blank"><i
                            class="fa fa-desktop"></i> Preview</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            @include('forms.admin.template_form',['disabled'=>false])
        </div>
        <div class="modal-footer">
            @permission(['update-templates','create-templates'])
            <button type="button" class="btn btn-default pull-left" data-toggle="modal" data-target="#md_template_tags"> View Tags </button>
                <a href="{{ route('admin.settings.templates.index') }}"
                   class="btn btn-default"><i class="fa fa-arrow-left"> Back</i> </a>
                <button type="submit" class="btn" name="_save" value="draft">Save</button>
                <button type="submit" class="btn btn-primary" name="_save" value="publish">Save & Publish</button>
            @endpermission
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@push('modals')
@widget('templateTags')
@endpush

@section('page_js')
    <script src="{{asset('plugins/tinymce/tinymce.min.js')}}" type="text/javascript"></script>
@endsection

@push('javascripts')
<script>
    $(function () {
        tinymce.init({
            selector: '#wysiwyg',
            height: 500,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code'
            ],
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
            content_css: '//www.tinymce.com/css/codepen.min.css'
        });
    })
</script>
@endpush





