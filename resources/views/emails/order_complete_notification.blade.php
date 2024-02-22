@extends('layouts.email')

@section('message_content')
    Hello,<br><br>

    Your order for the event <b>{{ $event->name }}</b> was successful.<br><br>

    Your tickets are attached to this email. You can also view you order details and download your tickets at: <a href="{{route('app.orders.preview', [$order->pk])}}"> Download Tickets</a>


    <h3>Order Details</h3>
    Order Reference: <b>{{$order->ref_no}}</b><br>
    Order Name: <b>{{$user->full_name}}</b><br>
    Order Date: <b>{{$order->created_at->toDayDateTimeString()}}</b><br>
    Order Email: <b>{{$user->email}}</b><br>

    <h3>Order Items</h3>
    <div style="padding:10px; background: #F9F9F9; border: 1px solid #f1f1f1;">
        <table style="width:100%; margin:10px;">
            <tr>
                <td>
                    <b>Ticket</b>
                </td>
                <td>
                    <b>Qty.</b>
                </td>
                <td>
                    <b>Price</b>
                </td>
                <td>
                    <b>Total</b>
                </td>
            </tr>
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
        </table>

        <br><br>
    </div>
    <br><br>
    Thank you
@stop
