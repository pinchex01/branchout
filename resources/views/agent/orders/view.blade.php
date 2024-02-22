@extends('layouts.agent')

<?php $vue = true ?>
<?php $maps = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-right">
                <a href="{{ route('agent.orders.index',[$organiser->slug, $order->id]) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back to list</a>
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
                                            {{ $attendee->ticket->name }}
                                        </td>
                                        <td>
                                            {{ $attendee->status }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
