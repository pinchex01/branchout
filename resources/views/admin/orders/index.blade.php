@extends('layouts.admin')
<?php $vue = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Manage Event Orders
            <div class="pull-right">
                <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#cs-filter"
                        aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-sliders"></i> Filter
                </button>
                <button class="btn btn-primary" data-toggle="modal" data-target="#md-create-order"><i class="fa fa-cart-plus"></i> Create Order</button>
                    @push('modals')
                        <div class="modal fade" id="md-create-order"  role="basic" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"> Create Order </h4>
                                    </div>
                                    <div class="modal-body">
                                        <manual-order-form></manual-order-form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endpush
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
                <form class="well">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Order No</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[order_no]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Order No',
                                ]) !!}
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Buyer Phone Number</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[phone]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Buyer Phone Number',
                                ]) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Event</label>
                            <div class="col-sm-10">
                                {!! Form::select('filters[event_id]',['' => "All"] + $events , null, [
                                'class' => 'form-control select2',
                                'label'=>"Event"
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Payment Type</label>
                            <div class="col-sm-10">
                                {!! Form::select('filters[payment]',['' => "All", 'paid' =>'Paid',  'free' => "Free"] , null, [
                                'class' => 'form-control select2',
                                'label'=>"Payment Type"
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-10">
                                {!! Form::select('filters[status]',['' => "All"] + ['paid' =>'Paid',  'pending' => "Not Paid", 'processing' => 'Processing'] , null, [
                                'class' => 'form-control select2',
                                'label'=>"Status"
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer filter-actions">
                        <button type="submit" class="btn btn-danger">
                            Filter
                        </button>
                        <a href="{{route('admin.orders.index')}}" class="btn btn-default">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-advance dt-responsive">
                    <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Event</th>
                        <th>Order Placed By</th>
                        <th>Order Date</th>
                        <th class="ammount">Amount Paid</th>
                        <th>Status</th>
                        <th width="15%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->ref_no }}</td>
                            <td>{{ $order->event }}</td>
                            <td>{{$order->user}}</td>
                            <td>{{$order->order_date}}</td>
                            <td class="ammount">{{$order->amount ? money($order->amount): 'FREE'}}</td>
                            <td>{!!$order->get_status_label()  !!}</td>
                            <td>
                                <a href="{{ route('admin.orders.view',[$order->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a>
                                @if($order->is_complete())
                                    <a href="{{ route('admin.orders.notify',[$order->id]) }}" class="btn btn-warning btn-xs"><i class="fa fa-envelope"></i> </a>
                                    @permission(['delete-orders'])
                                    <button data-toggle="modal" data-target="#md-cancel-order-{{$order->pk}}" class="btn btn-danger btn-xs" title="Cancel Order"><i class="fa fa-ban"></i> </button>
                                    <div class="modal fade" id="md-cancel-order-{{$order->pk}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <form  class="modal-content" method="post" action="{{ route('admin.orders.cancel', [$order->id]) }}">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                                aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="exampleModalLabel">Cancel Order</h4>
                                                </div>
                                                <div class="modal-body">
                                                    {!! csrf_field() !!}
                                                    <div class="form-group">
                                                      <label><input type="checkbox" name="refund" value="1" /> Issue refund. This will credit the user's wallet with the order amount. </label>
                                                    </div>
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
                                    @endpermission

                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <p class="text-center">
                                    No orders have been placed yet. <br>
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {!! $orders->render() !!}
            </div>
        </div>
    </div>
@endsection
