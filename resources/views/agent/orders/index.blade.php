@extends('layouts.agent')

<?php $vue = true ?>
<?php $maps = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Manage Event Orders
            <div class="pull-right">

            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
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
                        <th></th>
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
                            <td>{{$order->status}}</td>
                            <td><a href="{{ route('agent.orders.view',[$organiser->slug, $order->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a> </td>
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
        </div>
    </div>
@endsection
