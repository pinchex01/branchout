@extends('layouts.agent')


@section('page')

    <div class="panel panel-bank-details">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <p><strong>Account Name: </strong> {{$bank_account->name}}</p>
                    <p><strong>Account No: </strong> {{$bank_account->account_no}}</p>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <p><strong>Bank: </strong> {{$bank_account->bank ? : "MPESA Paybill" }}</p>
                    <p><strong>Currency: </strong> {{$bank_account->currency}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-success">
                <div class="card-icon bg-success text-center">
                    <i class="fa fa-money"></i>
                </div>

                <div class="card-block">
                    <div class="h5">KES. {{ number_format(array_get($summary, 'total_credit',0),2) }}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Total Credit</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-danger">
                <div class="card-icon bg-danger text-center">
                    <i class="fa fa-users "></i>
                </div>

                <div class="card-block">

                    <div class="h5">{{ number_format(array_get($summary, 'total_debit',0),2) }}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Total Debit</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="card bd-primary">
                <div class="card-icon bg-primary text-center">
                    <i class="fa fa-money"></i>
                </div>

                <div class="card-block">
                    <div class="h5">KES. {{ number_format(array_get($summary, 'total_balance',0),2) }}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Available Balance</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel with-nav-tabs panel-default">
        <div class="panel-heading panel-n-tab-heading hidden-xs">
            <h3 class="panel-title pull-left">
                @section('subject')
                    Details
                @show
            </h3>
            <div class="pull-right">
                @if (array_get($summary,'total_balance') && user()->can('create-settlements') )
                    <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#md_withdraw" type="button"><i class="fa fa-money"></i> withdraw</button>

                    @push('modals')
                    <form class="modal fade" id="md_withdraw" action="{{route('organiser.bank-accounts.withdraw',[$organiser->slug, $bank_account->id])}}"  method="post" role="basic" aria-hidden="true">
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
                                        of KES. {{ number_format(array_get($summary,'total_balance',0),2) }} to the selected bank account
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
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="{{menu_current_route('agent.bank-accounts.view','active')}}">
                    <a href="{{  route('agent.bank-accounts.view', [$organiser->slug,$bank_account->id])}}">
                        <i class="fa fa-money"></i> <strong>Settlements</strong></a>
                </li>

                <li class="{{menu_current_route('agent.bank-accounts.statements','active')}}">
                    <a href="{{  route('agent.bank-accounts.statements', [$organiser->slug,$bank_account->id]) }}">
                        <i class="fa fa-book"></i> <strong>Statements</strong></a>
                </li>
                <li class="{{menu_current_route('agent.bank-accounts.edit','active')}}">
                    <a href="{{ route('agent.bank-accounts.edit', [$organiser->slug,$bank_account->id]) }}">
                        <i class="fa fa-cogs"></i> <strong> Manage</strong></a>
                </li>
            </ul>
        </div>
        <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
            <h3 class="panel-title">
                @section('subject')
                    Details
                @show
            </h3>
        </div>
        <div class="panel-body">
            @yield('bank_account_info')
        </div>
    </div>

    @permission('update-banks')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">Settings</div>
        </div>
        <div class="panel-body">
            <form action="{{route('organiser.bank-accounts.view', [$organiser->slug, $bank_account->id])}}" method="post">
                {!! csrf_field() !!}
                <table class="table">
                    <tbody>
                    <tr>
                        <td width="70%"><b>Settlement Schedule</b></td>
                        <td width="">
                            <div class="form-group {{$errors->has('bank.settlement_schedule')? 'has-error': ''}}">
                                {!! Form::select('bank[settlement_schedule]',['on-demand'=>'On-Demand','real-time'=>'Real-Time'],$bank_account->settlement_schedule, array('class' => 'form-control')) !!}
                                {!! $errors->first('bank.settlement_schedule', '<span class="help-block">:message</span>') !!}
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>

        </div>
    </div>
    @endpermission
@stop

@permission('update-banks')

@push('modals')
<div class="modal fade" id="modal_bank" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <img src="{{asset('img/loading-spinner-grey.gif')}}" alt="" class="loading">
                <span> &nbsp;&nbsp;Loading... </span>
            </div>
        </div>
    </div>
</div>
@endpush
@endpermission
