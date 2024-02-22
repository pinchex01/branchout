@extends('layouts.admin_settings')

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="caption-subject bold uppercase"> Edit Bank Account Details</span>
            </div>
        </div>
        <div class="panel-body">
            {!! Form::model(['bank'=>$bank],['method'=>'POST','files'=>true]) !!}
            @include('forms.merchant.bank_form')
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
