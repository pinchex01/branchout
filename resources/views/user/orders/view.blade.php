@extends('layouts.user')

<?php $vue = true ?>
<?php $maps = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-body">
            @if($order->status  == 'paid')
            <div class="row">
                <div class="col-md-12 order_header">
                    <div class="text-center">
                        <i class="fa fa-check-circle fa-6x text-success"></i>
                    </div>
                    <h1>Thank you for your order!</h1>
                </div>
            </div>

            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="content event_view_order">
                        <div class="tools pull-right">
                            @if(!$order->is_complete() && !$order->has_expired_tickets())
                                <a href="{{ route('app.orders.checkout', $order->id) }}" class="btn btn-danger"><i class="fa fa-shopping-cart"></i> Pay Now </a>
                            @endif
                            <a href="{{ route('app.orders.list') }}" class="btn btn-default"> Back to list</a>
                        </div>
                        <div class="clearfix"></div>
                        <div class="order_details well">
                            <div class="row">
                                <div class="col-sm-4 col-xs-6">
                                    <b>First Name</b><br> {{$order->get_user_info()->first_name}}
                                </div>

                                <div class="col-sm-4 col-xs-6">
                                    <b>Last Name</b><br> {{$order->get_user_info()->last_name}}
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
                                    <b>Email</b><br> {{$order->get_user_info()->email}}
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
                                <tr class="danger">
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
                                        <b>Total</b>
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

                        <form  method="post" action="{{ route('app.tickets.batch_download') }}">
                        @if ($order->is_complete())
                            <button type="submit" name="action" value="download" class="btn btn-info"><i class="fa fa-download"></i> Download Tickets </button>
                        @endif
                            {!! csrf_field() !!}
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-advance dt-responsive">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th class="hidden-xs">Ticket #</th>
                                        <th class="hidden-xs">Ticket Type</th>
                                        <th>Holder Name</th>
                                        <th>Event</th>
                                        <th class="amount hidden-xs">Amount Paid</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($order->attendees as $ticket)
                                        <tr>
                                            <td>
                                            @if ($order->is_complete())
                                                <input type="checkbox" name="tickets[]" value="{{ $ticket->ref_no }}">
                                            @endif
                                            
                                            </td>
                                            <td class="hidden-xs">{{ $ticket->ref_no }}</td>
                                            <td class="hidden-xs">{{$ticket->ticket_name}}</td>
                                            <td>
                                                {{$ticket->full_name }}
                                                <small class="visible-xs text-muted">No. {{$ticket->ref_no}}</small>
                                            </td>
                                            <td>
                                                {{ $ticket->event_name }}
                                                <small class="visible-xs text-muted">{{$ticket->ticket_name}}</small>
                                            </td>
                                            <td class="amount hidden-xs">{{$ticket->price ? money($ticket->price): 'FREE'}}</td>
                                            <td>
                                            @if($order->is_complete())
                                                <a href="{{ route('app.tickets.download', $ticket->id) }}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-download"></i> download </a>
                                            @endif
                                                @if(!$ticket->check_in_time && $order->is_complete())
                                                    <button type="button" data-target="#ed_ticket_{{ $ticket->ref_no }}" data-toggle="modal"
                                                            class="btn btn-info btn-xs" title="Edit Ticket Info"><i class="fa fa-pencil-square-o"></i> </button>
                                                    @push('o_modals')
                                                    <div class="modal fade" id="ed_ticket_{{ $ticket->ref_no }}"  role="basic" aria-hidden="true">
                                                        <form class="modal-dialog" method="post" action="{{ route('app.tickets.edit', [$ticket->id])}}">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span></button>
                                                                    <h4 class="modal-title"> Edit Ticket Details </h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    {!! csrf_field() !!}
                                                                    <div class="form-group">
                                                                        <label>First Name</label>
                                                                        <input type="text" name="first_name" class="form-control" value="{{ $ticket->first_name}}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Last Name</label>
                                                                        <input type="text" name="last_name" class="form-control" value="{{ $ticket->last_name}}">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal"
                                                                            aria-label="Close">Cancel </button>
                                                                    <button type="submit" class="btn btn-primary"> Save</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    @endpush
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <p class="text-center">
                                                    No orders have been placed yet. <br>
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>


                        <h3>
                            Payments
                        </h3>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
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
@endsection
