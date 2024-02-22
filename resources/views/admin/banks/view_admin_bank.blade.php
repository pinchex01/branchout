@extends('layouts.admin')


@section('page')
    @include('admin.banks._admin_bank_account_info')


    <div class="panel with-nav-tabs panel-default">
        <div class="panel-heading panel-n-tab-heading hidden-xs">
            <h3 class="panel-title pull-left">Settlements</h3>
            <div class="pull-right">
                @if($summary->balance && user()->can('create-settlements'))
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#md_withdraw"><i class="fa fa-money"></i> withdraw</button>

                    @push('modals')
                    <form class="modal fade" id="md_withdraw" action="{{route('admin.banks.withdraw',[$bank->id])}}"  method="post" role="basic" aria-hidden="true">
                        {!! csrf_field() !!}
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Withdraw Money</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="note note-info">
                                        <i class="fa fa-info fa-2x"></i> <b>Note: </b> This action will send the total available balance
                                        of KES. {{ number_format($summary->balance,2) }} to bank account {{$bank}}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Continue</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endpush

                @endif
                <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#cs-filter"
                        aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-sliders"></i> Filter
                </button>

            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-heading">
            @include('admin.banks._admin_bank_account_menu')
        </div>
        <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
            <h3 class="panel-title">Settlements</h3>
        </div>
        <div class="panel-body">
            <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
                {!! Form::open(['method' => 'POST', 'class' => 'well']) !!}
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">From</label>
                        <div class="col-sm-5">
                            {!! Form::text('start', null, ['class' => 'form-control datetimepicker','placeholder'=>'Start Date']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">To</label>
                        <div class="col-sm-5">
                            {!! Form::text('end', null, ['class' => 'form-control datetimepicker','placeholder'=>'End Date']) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger btn-block">
                        Filter
                    </button>
                    <a href="{{route('admin.banks.view',[ $bank->id])}}" class="btn btn-default btn-block">
                        <i class="fa fa-times"></i> Clear
                    </a>
                </div>
                {!! Form::close() !!}
            </div>
            <table class="table table-striped table-hover dt-responsive">
                <thead>
                <tr>
                    <th>Txn. Date</th>
                    <th>Txn. ID.</th>
                    <th>Description</th>
                    <th>Amount</th>
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
                        <td>
                            <button type="button" class="btn btn-default btn-xs"
                                    data-target="#md_settlement_details_{{$settlement->id}}" data-toggle="modal">
                                <i class="fa fa-eye"></i> Details
                            </button>

                            @push('modals')
                            <div class="modal fade" id="md_settlement_details_{{$settlement->id}}" role="basic"
                                 aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">Settlement Details</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="well">
                                                <p><strong>Transaction ID: </strong> {{$settlement->id}}
                                                </p>
                                                <p><strong>Amount: </strong> KES {{$settlement->amount}}</p>
                                                <p><strong>Transaction Date: </strong> {{$settlement->created_at}}</p>
                                                <p><strong>Transaction Description: </strong> {{$settlement->notes}}</p>
                                                <p><strong>Account: </strong> {{ $settlement->account_no." - {$settlement->account_name} ($settlement->bank)" }}</p>
                                                <p><strong>Transaction Status: </strong> {!! $settlement->status !!}</p>

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">OK
                                            </button>
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
        </div>
    </div>

@stop

