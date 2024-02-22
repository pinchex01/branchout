@extends('layouts.user')

<?php $vue = true ?>
<?php $maps = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>My Tickets</h3>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover table-advance dt-responsive">
                  <thead>
                  <tr>
                      <th class="hidden-xs">Order #</th>
                      <th>Event</th>
                      <th class="hidden-xs">Order Date</th>
                      <th class="ammount">Amount <span class="hidden-xs">Paid</span></th>
                      <th>Status</th>
                      <th></th>
                  </tr>
                  </thead>
                  <tbody>
                  @forelse($orders as $order)
                      <tr>
                          <td class="hidden-xs">{{ $order->ref_no }}</td>
                          <td>
                            {{$order->event}}
                            <small class="visible-xs text-muted">#{{ $order->ref_no }}</small>
                          </td>
                          <td class="hidden-xs">{{$order->order_date}}</td>
                          <td class="ammount">{{$order->amount ? number_format($order->amount): 'FREE'}}</td>
                          <td>{!! $order->get_status_label() !!}</td>
                          <td>
                          <a href="{{ route('app.orders.view', $order->id) }}" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a>
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
                {!! $orders->render() !!}
            </div>
        </div>
    </div>
@endsection
