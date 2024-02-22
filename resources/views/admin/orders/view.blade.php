@extends('layouts.admin')

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-right">
                @permission(['delete-orders'])
                <button data-toggle="modal" data-target="#md-cancel-order-{{$order->pk}}" class="btn btn-danger" title="Cancel Order"><i class="fa fa-ban"></i> Cancel </button>
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
                @if(in_array($order->status, ['paid', 'complete']))
                    <a href="{{ route('admin.orders.notify',[$order->id]) }}" class="btn btn-primary"><i class="fa fa-envelope"></i> Send Ticket(s) </a>
                @endif
                <a href="{{ route('admin.orders.index') }}" class="btn btn-default"> Back to list</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="content event_view_order">
                        <div class="order_details well">
                            <div class="row">
                                <div class="col-sm-4 col-xs-6">
                                    <b>First Name</b><br> {{$order->user->first_name}}
                                </div>

                                <div class="col-sm-4 col-xs-6">
                                    <b>Last Name</b><br> {{$order->user->last_name}}
                                </div>

                                <div class="col-sm-4 col-xs-6">
                                    <b>Amount</b><br> {{ money($order->amount) }}
                                </div>

                                <div class="col-sm-4 col-xs-6">
                                    <b>Reference</b><br> {{$order->ref_no}}
                                </div>

                                <div class="col-sm-4 col-xs-6">
                                    <b>Date</b><br> {{$order->created_at->toDateTimeString()}}
                                </div>

                                <div class="col-sm-4 col-xs-6">
                                    <b>Phone</b><br> {{$order->user->phone}}
                                </div>
                                <div class="col-sm-4 col-xs-6">
                                    <b>Email</b><br> {{$order->user->email}}
                                </div>

                                <div class="col-sm-4 col-xs-6">
                                    <b>Status</b><br> {!! $order->get_status_label() !!}
                                </div>
                            </div>
                        </div>

                        <h3>
                            Order Items
                        </h3>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr class="info">
                                    <th>
                                        Ticket
                                    </th>
                                    <th>
                                        Quantity
                                    </th>
                                    <th>
                                        Price
                                    </th>
                                    <th>
                                        Total
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->order_items as $order_item)
                                    <tr>
                                        <td>
                                            {{$order_item->name}}
                                        </td>
                                        <td>
                                            {{$order_item->quantity}}
                                        </td>
                                        <td>
                                            @if((int)ceil($order_item->unit_price) == 0)
                                                FREE
                                            @else
                                                {{money($order_item->unit_price)}}
                                            @endif

                                        </td>
                                        <td>
                                            @if((int)ceil($order_item->unit_price) == 0)
                                                FREE
                                            @else
                                                {{money($order_item->total)}}
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <b>Sub Total</b>
                                    </td>
                                    <td colspan="2">
                                        {{money($order->amount)}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </div>

                        <h3>
                            Tickets
                        </h3>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <tbody>
                                @foreach($order->attendees as $attendee)
                                    <tr>
                                        <td>
                                            {{ $attendee->full_name }}
                                        </td>
                                        <td>
                                            <a href="mailto:{{$attendee->email}}">{{$attendee->email}}</a>
                                        </td>
                                        <td>{{$attendee->phone}}</td>
                                        <td>
                                            {{ $attendee->ticket->name }}
                                        </td>
                                        <td>
                                            {{ $attendee->status }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3> Payments</h3>
                                    <div class="tools">
                                        @if(!in_array($order->status, ['paid','complete']) && user()->can('create-payments'))
                                            <button class="btn btn-primary pb-10" data-toggle="modal" data-target="#md-c-p"><i class="fa fa-money"></i> Create Payment</button>
                                            @push('modals')
                                            <div class="modal fade" id="md-c-p" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <form  class="modal-content" method="post" action="{{ route('admin.orders.create_payment', [$order->id]) }}">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                                        aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="exampleModalLabel">Create Payment</h4>
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
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped pt-20">
                                            <thead>
                                            <tr class="danger">
                                                <th>#</th>
                                                <th>Channel</th>
                                                <th>Date Paid</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($order->payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->id }}</td>
                                                    <td>
                                                        {{ $payment->channel }}
                                                    </td>
                                                    <td>
                                                        {{ $payment->date_paid }}
                                                    </td>
                                                    <td>
                                                        {{ money($payment->amount) }}
                                                    </td>
                                                    <td>
                                                        {{ $payment->status }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5"> No payments have been made yet </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
