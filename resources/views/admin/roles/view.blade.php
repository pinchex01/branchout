@extends('admin.layouts.settings')

@section('settings')

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                Edit Group
            </div>
        </div>
        <div class="panel-body">
            {!! Form::model($role,['url'=>route('admin.roles.view',[$role->id]),'method'=>'PATCH','files'=>true]) !!}
            @include('forms.merchant.role_form')
            <div class="panel-footer">
                @permission('update-roles')
                <button  type="submit"  class="btn btn-primary">Save</button>
                @endpermission
                <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Back</a>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <!-- END Basic Information PORTLET-->

@stop

