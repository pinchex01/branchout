@extends('agent.bank-accounts.layouts.bank_account_info')

@section('subject', 'Bank Account Details')

@section('bank_account_info')
    <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
        {!! Form::open(['method' => 'POST', 'class' => 'well']) !!}
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">From</label>
                <div class="col-sm-10">
                    {!! Form::text('start', null, ['class' => 'form-control datetimepicker','placeholder'=>'Start Date']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">To</label>
                <div class="col-sm-10">
                    {!! Form::text('end', null, ['class' => 'form-control datetimepicker','placeholder'=>'End Date']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer filter-actions">
            <button type="submit" class="btn btn-danger">
                Filter
            </button>
            <a href="{{route('organiser.bank-accounts.view',[$organiser->slug,$bank_account->id])}}"
               class="btn btn-default">
                <i class="fa fa-times"></i> Clear
            </a>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover dt-responsive">
            <thead>
            <tr>
                <th>Txn. Date</th>
                <th>Txn. ID.</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>

            @forelse($settlements as $settlement)
                <tr>
                    <td>{{date('M d, Y',strtotime($settlement->created_at))}}</td>
                    <td>{{$settlement->id}}</td>
                    <td>{{$settlement->notes}}</td>
                    <td>{{number_format($settlement->amount,2)}}</td>
                    <td>{!! $settlement->get_status() !!}</td>
                    <td>
                        <button type="button" class="btn btn-default btn-xs" data-target="#md_settlement_details_{{$settlement->id}}" data-toggle="modal">
                            <i class="fa fa-eye"></i> Details
                        </button>

                        @push('modals')
                        <div class="modal fade" id="md_settlement_details_{{$settlement->id}}" role="basic" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Settlement Details</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="well">
                                            <p><strong>Transaction ID: </strong> {{$settlement->id}}</p>
                                            <p><strong>Amount: </strong> KES {{$settlement->amount}}</p>
                                            <p><strong>Transaction Date: </strong> {{$settlement->created_at}}</p>
                                            <p><strong>Transaction Description: </strong> {{$settlement->notes}}</p>
                                            <p><strong>Account: </strong> {{ $settlement->account_no." - {$settlement->account_name} ($settlement->bank)" }}</p>
                                            <p><strong>Transaction Status: </strong> {!! $settlement->status !!}</p>

                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div>

                        @endpush
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No records found</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {!! $settlements->render() !!}
    </div>
@stop
