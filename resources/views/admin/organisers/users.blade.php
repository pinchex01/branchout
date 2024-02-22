@extends('admin.organisers.layouts.organiser_details')

<?php $vue = true; ?>

@section('organiser_info')
    <div class="mt-10">
      @permission(['create-staffs'])
          <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#md-user-role"><i class="fa fa-user-plus"></i> Add User </button>
          @push('modals')
              <div class="modal fade" id="md-user-role"  role="basic" aria-hidden="true">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal"
                                      aria-label="Close">
                                  <span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title"> Add User </h4>
                          </div>
                          <div class="modal-body">
                              <user-role-form type="org" merchant_id="{{ $organiser->id }}"></user-role-form>
                          </div>
                      </div>
                  </div>
              </div>
          @endpush
      @endpermission
    </div>
    <div class="table-responsive">

        <table class="table table-striped table-hover table-condensed">
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Phone </th>
                <th>Email </th>
                <th>Role</th>
                <th>Status</th>
                <th> Action(s) </th>
            </tr>
            </thead>
            <tbody>
            @php($skipped = ($users->currentPage() * $users->perPage()) - $users->perPage())
            @forelse($users as $user)
                <tr>
                    <td>{{$loop->iteration + $skipped }}</td>
                    <td><a href="{{ route('admin.users.view',[$user->id]) }}">{{$user->full_name}}</a></td>
                    <td>{{$user->phone}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{ $user->merchant_roles->first()}}</td>
                    <td>{!! $user->status_label !!}</td>
                    <td>
                        <button class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> </button>
                        @permission('delete-staffss')
                            <button type="button" class="btn btn-danger btn-xs" title="Remove" data-toggle="modal" data-target="#modal_remove_staff_{{$user->pk}}"><i class="fa fa-ban"></i></button>
                            @push('modals')

                        <form class="modal fade" id="modal_remove_staff_{{$user->pk}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                              method="post" action="#">
                            {!! csrf_field() !!}
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                    aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="exampleModalLabel">Remove Staff</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you user you want to remove {{$user}} from your staff?</p>
                                        <input type="hidden" name="role_ids[]" value="">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary submit-btn ">Yes</button>
                                    </div>
                                </div>
                            </div>
                        </form>


                        @endpush
                        @endpermission

                        

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No users assigned to this group</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {!! $users->render() !!}
    </div>
@stop
