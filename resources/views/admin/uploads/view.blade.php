@extends('layouts.admin')

@section('page')

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">
                #{{$upload->id}} - {{ $upload->name }}  Details

            </div>
            <div class="tools pull-right">
                <a href="{{ route('admin.uploads.index')}}"> <i class="fa fa-arrow-left"></i> Backt to list </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-hover table-condensed">

                <tbody>
                <tr>
                    <td><strong>Name: </strong></td>
                    <td>{{$upload->name}}</td>
                </tr>
                <tr>
                    <td><strong>Type: </strong></td>
                    <td>{{$upload->type}}</td>
                </tr>
                <tr>
                    <td><strong>Size: </strong></td>
                    <td>{{$upload->size}}</td>
                </tr><tr>
                    <td><strong>Uploaded By: </strong></td>
                    <td>{{$upload->author->name}}</td>
                </tr>
                <tr>
                    <td><strong>Date Uploaded: </strong></td>
                    <td>{{$upload->created_at}}</td>
                </tr>
                <tr>
                    <td><strong>Actions: </strong></td>
                    <td>
                        <a href="{{ route('admin.uploads.download', $upload->id) }}" class="btn btn-primary">
                            <i class="fa fa-download"></i> Download </a>
                        <button class="btn btn-danger" data-toggle="modal" data-target="#frm-df-{{$upload->id}}">
                            <i class="fa fa-trash"></i> Delete </button>
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
                </tbody>
            </table>
        </div>
    </div>
    <!-- END Basic Information PORTLET-->

@stop

