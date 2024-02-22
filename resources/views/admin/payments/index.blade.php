@extends('layouts.admin')

@section('page')


    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">
                <span class="caption-subject bold uppercase"> Payments </span>
            </div>
            <div class="tools pull-right">
                <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#cs-filter" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-sliders"></i> Filter
                </button>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
                <form class="well">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Transaction No</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[payment_key]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Transaction No',
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Transaction Ref</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[payment_ref]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Transaction Ref',
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Phone Number</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[phone]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Phone No 254712345678',
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Channel</label>
                            <div class="col-sm-10">
                                {!! Form::select('filters[channel]',['' => "All",'mpesa' => "MPESA", 'points' => 'Points', 'wallet' => 'Wallet', 'card' =>'Card'] , null, [
                            'class' => 'form-control',
                            'label'=>"Channel"
                            ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-10">
                                {!! Form::select('filters[status]',['' => "All",'pending' => "Pending", 'processing' => 'Processing', 'paid' => 'Paid', 'complete' =>'Complete'] , null, [
                            'class' => 'form-control',
                            'label'=>"Status"
                            ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">
                            Filter
                        </button>
                        <a href="{{route('admin.payments.index')}}" class="btn btn-default">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>

            <table class="table table-striped table-hover dt-responsive">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Txn No</th>
                    <th>Txn Ref</th>
                    <th>Channel</th>
                    <th>Phone No.</th>
                    <th>Amount</th>
                    <th>Date Paid</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->payment_key }}</td>
                        <td>{{ $payment->payment_ref }}</td>
                        <td>{{ $payment->channel }}</td>
                        <td>{{ $payment->order ? $payment->order->phone : 'N/A' }}</td>
                        <td>{{number_format($payment->amount)}}</td>
                        <td>{{$payment->created_at}}</td>
                        <td>{!! $payment->get_status_label() !!}</td>
                        <td>
                            <a class="btn btn-primary btn-xs" href="#" title="View Receipt"><i class="fa fa-eye"></i>  </a>
                            @if(!in_array($payment->status, ['paid','complete']))
                                <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#md-c-p" title="Complete Payment"><i class="fa fa-check-square-o"></i> </button>
                                @push('modals')
                                <div class="modal fade" id="md-c-p" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <form  class="modal-content" method="post" action="{{ route('admin.payments.complete', [ $payment->id]) }}">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="exampleModalLabel">Complete Payment</h4>
                                            </div>
                                            <div class="modal-body">
                                                {!! csrf_field() !!}
                                                <div class="note note-warning">
                                                    This action is irreversible. Are you sure you want to proceed
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal"> No</button>
                                                <button type="submit" class="btn btn-primary"> Yes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endpush
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">No payment records</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $payments->render() !!}
        </div>
    </div>

@stop

