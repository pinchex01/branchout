@extends('admin.layouts.settings')

@section('settings')
    <div class="tools">
        @permission('create-roles')
        <button class="btn btn-primary" data-toggle="modal" data-target="#modal_add_role"><i
                    class="fa fa-plus"></i> <span class="hidden-xs">New Group</span></button>
        @push('modals')
        <form class="modal fade" id="modal_add_role" role="basic" aria-hidden="true"
              action="{{route('admin.roles.new')}}" method="post">
            {!! csrf_field() !!}
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add Group</h4>
                    </div>
                    <div class="modal-body">
                        @include('admin.roles.forms.role_form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"
                                aria-label="Close">Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </div>
                </div>
            </div>
        </form>
        @endpush
        @endpermission
    </div>
    <div class="table-responsive">

        <table class="table table-striped table-hover dt-responsive">
            <thead>
            <tr>
                <td>#</td>
                <th>Name</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>


            @forelse($roles  as $role)
                <tr>
                    <td>{{$role->id}}</td>
                    <td>
                        @if(user()->can(['update-roles','view-roles']))
                            <a href="{{route('admin.roles.view',[$role->id])}}">{{$role->display_name}}</a>
                        @else
                            {{$role->display_name}}
                        @endif
                    </td>
                    <td>{{$role->description}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No records found</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {!! $roles->render() !!}
    </div>
@endsection

