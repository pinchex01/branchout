@extends('layouts.admin')


@section('page')

    <div class="row">
        <div class="col-md-12">
            <!-- END Basic Information PORTLET-->
            <div class="panel with-nav-tabs panel-default">
                <div class="panel-heading panel-n-tab-heading hidden-xs">
                    <h4 class="panel-title pull-left">User Details</h4>
                    <div class="pull-right">

                        @permission('create-staffs')
                        <button class="btn btn-primary" data-target="#md-edit-user" data-toggle="modal" ><i class="fa fa-pencil"></i> Edit </button>
                        @push('modals')
                            <div class="modal fade" id="md-edit-user"  role="basic" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                <span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"> Edit User </h4>
                                        </div>
                                        <div class="modal-body">
                                            <user-form user_pk="{{ $user->pk }}"></user-form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpush
                        @endpermission
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-heading">
                    @include('admin.users._user_details_menu')
                </div>
                <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
                    <h4 class="panel-title pull-left">User Details</h4>
                    <div class="pull-right">

                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body table-settings">
                    <div class="row">
                        <div class="col-sm-4 col-xs-12">
                            <img src="{{ $user->getAvatar() }}" name="aboutme" width="140" height="140" class="user-detail-avatar">
                        </div>
                        <div class="col-sm-8 col-xs-12">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td>Full Name</td>
                                    <td><b>{{ $user->full_name }}</b></td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td><b>{{ $user->id_number }}</b></td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>
                                        <b>{{ $user->phone }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>
                                        <b>{{ $user->email }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td><b>{{ $user->gender }}</b></td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td><b>{{ $user->status }}</b></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@stop

@push('javascripts')
<script>
    $(function() {
        $('.st_edit').editable();
    });
</script>
@endpush
