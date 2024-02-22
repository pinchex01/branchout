@extends('layouts.organiser')

<?php $vue = true ?>
<?php $maps = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Manage Payments
            <div class="pull-right">
                <button data-toggle="modal" data-target="#md_bc_new" class="btn btn-primary btn-sm" ><i class="fa fa-plus"></i> Add Bank Account </button>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover dt-responsive" width="100%">
                    <thead>
                    <tr>
                        <th>Account Name</th>
                        <th>Account No</th>
                        <th>Bank</th>
                        <th>Collected</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bank_accounts as $bank)
                        <tr>
                            <td> <a href="{{route('organiser.bank-accounts.view',[$organiser->slug, $bank->id])}}">
                                    {{$bank->name}}</a>
                                @if ($bank->is_default)<span class="label label-success">Default</span> @endif
                            </td>
                            <td>{{ $bank->type =='bank'?$bank->masked_account_no: $bank->account_no}}</td>
                            <td>{{$bank->bank ? : 'Paybill'}}</td>
                            <td>{{ number_format($bank->account->credit) }}</td>
                            <td>{{ number_format($bank->account->balance) }}</td>
                            <td>{!! $bank->status_label !!}</td>
                            <td>
                                <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!! $bank_accounts->render() !!}
            </div>
        </div>
    </div>
@endsection

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

