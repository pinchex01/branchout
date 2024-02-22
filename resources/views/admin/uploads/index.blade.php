@extends('layouts.admin')

@section('breadcrumbs')
    {!! render_breadcrumbs([
        [
        'name'=>'Uploads',
        'link'=>route('admin.uploads.index')
        ]
    ]) !!}
@endsection

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">
                <span class="caption-subject bold uppercase"> Uploads </span>
            </div>
            <div class="tools pull-right">
                <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#cs-filter" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-sliders"></i> Filter
                </button>

            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
                <form class="well">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Landlord ID</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[type]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Upload Type',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer filter-actions">
                        <button type="submit" class="btn btn-danger ">
                            Filter
                        </button>
                        <a href="{{route('admin.uploads.index')}}" class="btn btn-default">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-condensed">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Size </th>
                        <th>Uploaded By</th>
                        <th> Action(s) </th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($uploads as $upload)
                        <tr>
                            <td><a href="{{ route('admin.uploads.view', [$upload->id]) }}">{{$upload->name}}</a></td>
                            <td>{{$upload->type}}</td>
                            <td>{{$upload->size}}</td>
                            <td>{{ $upload->author->full_name }}</td></th>
                            <td>
                                        <span class="dropdown">
                                            <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                Action
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                <li><a href="{{ route('admin.uploads.view', [$upload->id]) }}"> view</a> </li>
                                                <li><a href="{{ route('admin.uploads.download', [$upload->id]) }}"> download</a> </li>
                                                <li><a href="#" data-target="#frm-df-{{$upload->id}}" data-toggle="modal"> delete</a> </li>
                                            </ul>
                                        </span>

                                @push('modals')
                                <form class="modal fade" id="frm-df-{{$upload->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                      method="post" action="{{route('admin.uploads.delete',[$upload->id])}}">
                                    {!! csrf_field() !!}
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="exampleModalLabel">Confirm Delete Upload</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this upload?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary submit-btn">Yes, Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                @endpush

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No  uploads</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {!! $uploads->render() !!}
        </div>
    </div>
@stop
