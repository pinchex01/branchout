@extends('agent.bank-accounts.layouts.bank_account_info')


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
            <button type="submit" class="btn btn-info" name="export" value="pdf">
                <i class="fa fa-download"></i> Export PDF
            </button>
            <a href="{{route('organiser.bank-accounts.statements',[$organiser->slug,$bank_account->id])}}"
               class="btn btn-default">
                <i class="fa fa-times"></i> Clear
            </a>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="table-responsive">
        <table class="table table-striped  table-hover dt-responsive" width="100%">
            <thead>
            <tr>
                <th>Rct. No</th>
                <th>Txn. Date</th>
                <th>Description</th>
                <th>Money In</th>
                <th>Money Out</th>
                <th>Balance</th>
            </tr>
            </thead>
            <tbody>

            @forelse($items as $statement)
                <tr>
                    <td>{{$statement->ref}}</td>
                    <td>{{$statement->txn_date}}</td>
                    <td>{{$statement->notes}}</td>
                    <td>{{number_format($statement->credit,2)}}</td>
                    <td>{{number_format($statement->debit,2)}}</td>
                    <td>{{number_format($statement->balance,2)}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No transactions found for the period specified</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {!! $items->render() !!}
    </div>
@stop
