@extends('admin.layouts.settings')



@section('settings')

    {!! Form::model($role,['url'=>route('admin.settings.merchant-roles.view',[$role->id]),'method'=>'PATCH','files'=>true]) !!}
            @include('admin.merchant-roles._merchant_role_form')
            <div class="panel-footer">
                @permission('update-roles')
                <button  type="submit"  class="btn btn-primary">Save</button>
                @endpermission
                <a href="{{ route('admin.settings.merchant-roles.index') }}" class="btn btn-default">Back</a>
            </div>
            {!! Form::close() !!}

@stop

