@extends('admin.organisers.layouts.organiser_details')
<?php $vue = true  ?>
@push('organiser_actions')
<div class="pull-right">
    <button data-toggle="modal" data-target="#md_bc_new" class="btn btn-primary btn-sm" ><i class="fa fa-plus"></i> Add Bank Account </button>
</div>
@endpush
@section('organiser_info')
    <table class="table table-striped table-hover dt-responsive" width="100%">
        <thead>
        <tr>
            <th></th>
            <th>Account Name</th>
            <th>Account No</th>
            <th>Bank</th>
            <th>Currency</th>
            <th>Available Balance</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php($skipped = ($bank_accounts->currentPage() * $bank_accounts->perPage()) - $bank_accounts->perPage())
        @foreach($bank_accounts as $bank_account)
            <tr>
                <td>{{$loop->iteration + $skipped }}</td>
                <td> <a href="{{route('admin.bank-accounts.view',[$bank_account->id])}}">
                        {{$bank_account->name}}</a>
                    @if ($bank_account->is_default)<span class="label label-success">Default</span> @endif
                </td>
                <td>{{ $bank_account->account_type =='bank'?$bank_account->masked_account_no: $bank_account->account_no}}</td>
                <td>{{$bank_account->bank ? : 'Paybill'}}</td>
                <td>{{$bank_account->currency}}</td>
                <td>{{ number_format($bank_account->account->balance) }}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a href="{{route('admin.bank-accounts.view',[$bank_account->id])}}"> view </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {!! $bank_accounts->render() !!}
@stop

@push('modals')
<div class="modal fade" id="md_bc_new" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Create Bank Account</h4>
            </div>
            <div class="modal-body">
                <bank-account-form owner_id="{{$organiser->id}}" owner_type="{{\App\Models\BankAccount::TYPE_ORGANISER}}"></bank-account-form>
            </div>
        </div>
    </div>
</div>
@endpush


