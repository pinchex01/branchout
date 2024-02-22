@extends('layouts.admin')

@section('page')

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">
                Edit Tariff -  {{$tariff->name}}
            </div>
            <div class="tools pull-right">
                <a href="{{ route('admin.settings.tariffs.index')}}" class="btn btn-default"> <i class="fa fa-arrow-left"></i> Back to list </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            {!! Form::model($tariff,['url'=>route('admin.settings.tariffs.edit',[$tariff->id]),'method'=>'POST','files'=>true]) !!}
            @include('forms.admin.tariff_form')
            <div class="modal-footer">

                <a class="btn btn-default" href="{{ route('admin.settings.tariffs.index') }}"> Cancel</a>
                <button type="submit"
                        class="btn btn-primary "> Save
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@stop

