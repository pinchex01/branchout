@extends('admin.layouts.settings')

@section('settings')
    <form method="post">
        {!!  csrf_field() !!}
        <div class="form-group {{$errors->has('ticket_html')? 'has-error': ''}} mt-30">
            {!! Form::textarea('ticket_html', settings('ticket_html'), ['class' => 'form-control', 'id' => 'ticket_html']) !!}
            {!! $errors->first('ticket_html', '<span class="help-block">:message</span>') !!}
        </div>
        <button name="submitbtn" class="btn btn-primary"><i class="fa fa-save"></i> Save Changes</button>
    </form>
@stop

@section('panels')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3>Ticket Preview</h3>
        <div class="pull-rig"></div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body" id="ticket_preview_box" style="min-height: 350px; width: 100%; margin-top: 5px;">
        <!-- <iframe src="{# route('admin.settings.ticket_preview') #}" height="300" width="100%"></iframe> -->
    </div>
</div>
@endsection

@push('page_js')
    <script src="{{asset('plugins/tinymce/tinymce.min.js')}}" type="text/javascript"></script>
@endpush

@push('page_scripts')
<script>
    $(function () {
        tinymce.init({
            selector: '#ticket_htmll',
            height: 500,
            plugins: [
                'save advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code codesample'
            ],
            codesample_languages: [
                {text: 'HTML/XML', value: 'markup'},
                {text: 'JavaScript', value: 'javascript'},
                {text: 'CSS', value: 'css'},
                {text: 'PHP', value: 'php'},
                {text: 'Ruby', value: 'ruby'},
                {text: 'Python', value: 'python'},
                {text: 'Java', value: 'java'},
                {text: 'C', value: 'c'},
                {text: 'C#', value: 'csharp'},
                {text: 'C++', value: 'cpp'}
            ],
            toolbar: 'save insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | codesample',
            content_css: '//www.tinymce.com/css/codepen.min.css'
        });

        get_ticket_preview();

        function get_ticket_preview() {
            $.get('{{ route('admin.settings.ticket_preview') }}', function(response){
                $('#ticket_preview_box').html(response);
            });
        }
    })
</script>
@endpush
