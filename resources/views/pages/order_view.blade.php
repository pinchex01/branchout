@extends('layouts.page')

@section('content')
    <div class="container" style="margin-top: 200px">
        <div class="panel panel-default">
            <div class="panel-body">
                @if($order->status  == 'paid')
                    <div class="row">
                        <div class="col-md-12 order_header">
                            <div class="text-center">
                                <i class="fa fa-check-circle fa-6x text-success"></i>
                            </div>
                            <h1>Thank you for your order!</h1>
                            <h2>
                                Your tickets and a
                                confirmation email have been sent to you. Check your spam folder as well
                            </h2>
                        </div>
                    </div>

                @endif
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
                                        <b>Date</b><br> {{$order->created_at}}
                                    </div>

                                    <div class="col-sm-4 col-xs-6">
                                        <b>Email</b><br> {{$order->get_user_info()->email}}
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
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <tr class="danger">
                                        <th>Ticket #</th>
                                        <th>Name</th>
                                        <th>Ticket</th>
                                        <th>Check-In</th>
                                        <th>Action(s)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($order->attendees as $attendee)
                                        <tr>
                                            <td>{{ $attendee->ref_no }}</td>
                                            <td>
                                                {{ $attendee->full_name }}
                                            </td>
                                            <td>
                                                {{ $attendee->ticket->name }}
                                            </td>
                                            <td>
                                                {{ $attendee->status }}
                                            </td>
                                            <td>
                                                <a href="{{ route('app.tickets.preview_download', $attendee->pk) }}">
                                                    <i class="fa fa-download"></i> download ticket
                                                </a>
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
    </div>
@endsection
