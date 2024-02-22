@extends('layouts.admin')

<?php $vue = true; ?>
@section('page')

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">Manage Users</div>
            <div class="pull-right">
                @permission('create-staffs')
                <a class="btn btn-primary" href="#md-create-user" data-toggle="modal" ><i class="fa fa-plus"></i> User</a>
                @push('modals')
                    <div class="modal fade" id="md-create-user"  role="basic" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title"> Create User </h4>
                                </div>
                                <div class="modal-body">
                                    <user-form></user-form>
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
        <div class="panel-body role-box-container ">
            <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
                {!! Form::open(['method' => 'GET', 'class' => 'well']) !!}
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
                <div class="modal-footer filter-actions">
                    <button type="submit" class="btn btn-danger ">
                        Filter
                    </button>
                    <a href="{{route('admin.users.index')}}" class="btn btn-default">
                        <i class="fa fa-times"></i> Clear
                    </a>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="table-responsive">
              <table class="table table-striped table-hover table-condensed">
                  <thead>
                  <tr>
                      <th></th>
                      <th>Name</th>
                      <th>Phone </th>
                      <th>Username </th>
                      <th>Date Joined</th>
                      <th>Status</th>
                      <th width="15%"> Action(s) </th>
                  </tr>
                  </thead>
                  <tbody>
                  @php($skipped = ($users->currentPage() * $users->perPage()) - $users->perPage())
                  @forelse($users as $user)
                      <tr>
                          <td>{{$loop->iteration + $skipped }}</td>
                          <td><a href="{{ route('admin.users.view',[$user->id]) }}">{{$user->full_name}}</a></td>
                          <td>{{$user->phone}}</td>
                          <td>{{$user->id_number}}</td>
                          <td>{{date('M d, Y', strtotime($user->created_at))}}</td>
                          <td>{!! $user->status_label !!}</td>
                          <td>
                              <a class="btn btn-primary btn-xs" href="{{ route('admin.users.view',[$user->id]) }}" title="View details"><i class="fa fa-eye"></i> </a>
                              <a class="btn btn-danger btn-xs" href="{{ route('admin.users.reset',[$user->id]) }}" title="Reset password"><i class="fa fa-lock"></i> </a>
                          </td>
                      </tr>
                  @empty
                      <tr>
                          <td colspan="8">No users</td>
                      </tr>
                  @endforelse
                  </tbody>
              </table>
            </div>


            {!! $users->render() !!}

        </div>
    </div>

@stop


@push('modals')

@endpush



@push('javascripts')
<script>

    $(function () {


    });
</script>
@endpush
