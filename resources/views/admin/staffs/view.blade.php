@extends('admin.layouts.merchant')

@section('breadcrumbs')
    {!! render_breadcrumbs([
        [
        'name'=>'Staff',
        'link'=>route('merchant.staffs.index',[$merchant->slug])
        ],[
        'name'=>'Staff Details',
        'link'=>route('merchant.staffs.view',[$merchant->slug,$user->id] )
        ]
    ]) !!}
@endsection

<?php Assets::add(['themes/metronic/assets/pages/css/profile.min.css', 'themes/metronic/assets/pages/css/profile.min.js']) ?>

@section('page')

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="profile-sidebar">
                <!-- PORTLET MAIN -->
                <div class="portlet light profile-sidebar-portlet bordered">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-userpic">
                        <img src="{{$user->getAvatar()}}" height="50" class="img-responsive" alt=""></div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name"> {{$user}} </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->

                    <!-- SIDEBAR MENU -->
                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li class="active">
                                <a href="#">
                                    <i class="icon-home"></i> Personal Details </a>
                            </li>
                        </ul>
                    </div>
                    <!-- END MENU -->
                </div>
                <!-- END PORTLET MAIN -->
                <!-- PORTLET MAIN -->
                <!-- END PORTLET MAIN -->
            </div>
            <!-- END BEGIN PROFILE SIDEBAR -->
            <!-- BEGIN PROFILE CONTENT -->
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN PORTLET -->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption caption-md">
                                    <i class="icon-bar-chart theme-font hide"></i>
                                    <span class="caption-subject font-blue-madison bold uppercase">Personal Details</span>
                                    <span class="caption-helper hide"> </span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                {!! Form::model($user->profile,['url'=>route('merchant.staffs.view',[$merchant->slug,$user->id]),'method'=>'POST','files'=>true]) !!}
                                @include('merchant.staffs.forms.form_staff_basic',['disabled'=>true])
                                <div class="panel-footer">
                                    @permission('update-staffs')
                                    <button id="cmd_edit" class="btn btn-primary">Edit</button>
                                    <button id="cmd_save" data-form="service-defs-form" type="submit"
                                            class="btn btn-primary hide">Save
                                    </button>
                                    @endpermission
                                    <a href="{{ route('merchant.staffs.index', [$merchant->slug]) }}"
                                       class="btn btn-default">Back</a>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <!-- END PORTLET -->

                        <!-- BEGIN PORTLET -->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption caption-md">
                                    <i class="icon-bar-chart theme-font hide"></i>
                                    <span class="caption-subject font-blue-madison bold uppercase">Roles</span>
                                    <span class="caption-helper hide"> </span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="mb-10">
                                    @permission(['update-staffs','delete-staffs'])
                                    <a class="btn btn-primary" href="#modal_edit_staff_roles" data-toggle="modal"><i
                                                class="fa fa-pencil-square-o"></i> Manage</a>
                                    @endpermission
                                </div>


                                <table class="table table-striped table-bordered table-hover dt-responsive"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <td>#</td>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @forelse($user->roles  as $role)
                                        <tr>
                                            <td>{{$role->id}}</td>
                                            <td>
                                                <a href="{{route('merchant.roles.view',[$merchant->slug,$role->id])}}">{{$role->display_name}}</a>
                                            </td>
                                            <td>{{$role->name}}</td>
                                            <td>{{$role->description}}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">No records found</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- END PORTLET -->

                    </div>
                </div>
            </div>
            <!-- END PROFILE CONTENT -->
        </div>
    </div>

@stop

@push('modals')
<form class="modal fade has-date-picker" id="modal_edit_staff_roles"
      action="{{route('merchant.staffs.edit.roles',[$merchant->slug,$user->id])}}" method="post" role="basic"
      aria-hidden="true">
    {!! csrf_field() !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Manage Staff Roles</div>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Select Role(s)</label>

                    @foreach($roles as $role)
                        <div class="">
                            <label>
                                <input type="checkbox" name="role_ids[]"
                                       value="{{ $role->id }}"/> {{ $role->display_name }}
                            </label>
                        </div>
                    @endforeach
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
    </div>
</form>

@endpush

