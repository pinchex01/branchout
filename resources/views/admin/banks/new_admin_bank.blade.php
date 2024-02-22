@extends('layouts.admin')


@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">
                <span class="caption-subject bold uppercase"> Add Bank Account </span>
            </div>
            <div class="pull-right">
                <a class="btn btn-default" href="{{ route('admin.banks.index') }}"><i class="fa fa-arrow-left"> Back</i> </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <form class="" id="modal_bank_add" action="{{route('admin.banks.store')}}"  method="post" enctype="multipart/form-data">
                {!! csrf_field() !!}
                @include('forms.merchant.bank_form')
                <div class="modal-footer">
                    <a class="btn btn-default" href="{{ route('admin.banks.index') }}"> Cancel</a>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>

    </div>
@stop