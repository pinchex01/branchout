@extends('layouts.admin')


@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title pull-left">Bank Details</h4>
            <div class="pull-right">
                <a class="btn btn-default" href="{{ route('admin.settings.system-banks.index') }}"><i class="fa fa-arrow-left"></i> Back </a>
                @permission('update-banks')
                    <button class="btn btn-primary"  data-toggle="modal" data-target="#modal_bank_edit"><i class="fa fa-pencil"></i> Edit</button>
                @endpermission
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <strong>Name: </strong> {{$bank->name}} <br>
            <strong>Paybill: </strong> {{$bank->paybill}} <br>
            <strong>Status: </strong> {!! $bank->status_label !!} <br>
        </div>

    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">Accounts</h4>

        </div>
        <div class="panel-body">
            <table class="table table-striped table-hover dt-responsive">
                <thead>
                <tr>
                    <th>Account Name</th>
                    <th>Account No</th>
                    <th>Branch</th>
                    <th>Currency</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bank->accounts as $account)
                    <tr>
                        <td>{{$account->name}}</td>
                        <td>{{$account->masked_account_no}}</td>
                        <td>{{$account->branch}}</td>
                        <td>{{$account->currency}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

@stop


@push('modals')
{!! Form::model(['bank'=>$bank],['url'=>route('admin.settings.system-banks.edit',[$bank->id]),'method'=>'POST','files'=>true,'id'=>'modal_bank_edit','class'=>'modal fade']) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Bank</h4>
            </div>
            <div class="modal-body">
                @include('forms.admin.bank_form')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i
                            class="fa fa-times-circle-o"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
{!! Form::close() !!}
@endpush

