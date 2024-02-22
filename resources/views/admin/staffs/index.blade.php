@extends('admin.layouts.settings')

<?php $vue  = true; ?>
@section('page')

<div class="panel with-nav-tabs panel-default">
  <div class="panel-heading panel-n-tab-heading hidden-xs">
    <div class="panel-title pull-left">Manage Staff</div>
    <div class="pull-right">
      @permission(['create-staffs'])
          <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#md-user-role"><i class="fa fa-user-plus"></i> Add Staff </button>
          @push('modals')
              <div class="modal fade" id="md-user-role"  role="basic" aria-hidden="true">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal"
                                      aria-label="Close">
                                  <span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title"> Add Staff </h4>
                          </div>
                          <div class="modal-body">
                              <user-role-form></user-role-form>
                          </div>
                      </div>
                  </div>
              </div>
          @endpush
      @endpermission
        <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#cs-filter" aria-expanded="false" aria-controls="collapseExample">
            <i class="fa fa-sliders"></i> Filter
        </button>
    </div>
    <div class="clearfix"></div>
  </div>
                <div class="panel-heading">
                    @include('admin.settings._settings_menu')
              </div>
              <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
                <div class="panel-title pull-left">Manage Staff</div>
                <div class="pull-right">
                  @permission(['create-staffs'])
                      <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#md-user-role"><i class="fa fa-user-plus"></i> Add User </button>
                  @endpermission
                    <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#cs-filter" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa fa-sliders"></i> Filter
                    </button>
                </div>
                <div class="clearfix"></div>
              </div>
              <div class="panel-body role-box-container table-settings">
                <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
                    <form class="well">
                        <div class="form-horizontal">
                          <div class="form-horizontal">
                              <div class="form-group">
                                  <label class="col-sm-2 control-label">Search Term.</label>
                                  <div class="col-sm-10">
                                      {!! Form::text('filters[q]', null, [
                                  'class' => 'form-control',
                                  'placeholder'=> 'Phone (254XXX123123) or Email, Username or Name',
                                  ]) !!}
                                  </div>
                              </div>
                          </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> Group</label>
                                <div class="col-sm-10">
                                    {!! Form::select('filters[role_id]',['' => "All"] + $roles->pluck('name','id')->toArray() , null, [
                                    'class' => 'form-control select2',
                                    'label'=>"Role"
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">
                                Filter
                            </button>
                            <a href="{{route('admin.settings.staffs.index')}}" class="btn btn-default">
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
                          <th>Phone </th>
                          <th>Email </th>
                          <th> Action(s) </th>
                      </tr>
                      </thead>
                      <tbody>
                      @forelse($users as $user)
                          <tr>
                              <td>{{$user->full_name}}</td>
                              <td>{{$user->phone}}</td>
                              <td>{{$user->email}}</td>
                              <td>
                                  <span class="dropdown">
                                      <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                          switch
                                          <span class="caret"></span>
                                      </button>
                                      <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                          @foreach($roles as $_role)
                                                  <li>
                                                      <a href="#" class="edit-role" data-form="#form_{{$user->id}}_{{$_role->id}}">{{$_role->display_name}}</a>
                                                      <form id="form_{{$user->id}}_{{$_role->id}}" class="hide" method="post" action="{{route('admin.settings.staffs.change',[$user->id])}}">
                                                          {!! csrf_field() !!}
                                                          <input type="hidden" name="role_ids[]" value="{{$_role->id}}">
                                                      </form>
                                                  </li>
                                          @endforeach
                                      </ul>
                                  </span>
                                  <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal_remove_staff_{{$user->id}}"> <i class="fa fa-times"></i> </button>

                                  @push('modals')

                                  <form class="modal fade" id="modal_remove_staff_{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                        method="post" action="{{route('admin.settings.staffs.remove',[$user->id])}}">
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

                              </td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="5">No users assigned to this group</td>
                          </tr>
                      @endforelse
                      </tbody>
                  </table>


                </div>


              </div>

              </div>


@stop
