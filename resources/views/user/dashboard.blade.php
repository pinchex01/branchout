@extends('layouts.user')

<?php $maps = true ?>

@push('page_css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/fullcalendar/fullcalendar.min.css') }}">
@endpush

@section('page')
  @if(!user()->organisers->where('type','sales-agent')->count())
      <div class="visible-xs col-xs-12 mb-20">
          <a href="{{ route('account.organisers.new-sales-agent') }}" class="btn btn-danger btn-block"><i class="fa fa-user-secret"></i> Become an Agent</a>
      </div>
  @endif

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-success" data-step="1" data-intro="{{ trans('intro.user.total_paid') }}" data-position='right'>
                <div class="card-icon bg-success text-center">
                    <i class="fa fa-calendar-plus-o"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{number_format($summary->get('total_tickets'))}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Tickets Bought</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-danger" data-step="2" data-intro="{{ trans('intro.user.total_arrears') }}" data-position='right'>
                <div class="card-icon bg-danger text-center">
                    <i class="fa fa-calendar-times-o "></i>
                </div>

                <div class="card-block">
                    <div class="h5">KES. {{number_format($summary->get('spent'))}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Total Spent</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="card bd-primary" data-step="3" data-intro="{{ trans('intro.user.total_services') }}" data-position='right'>
                <div class="card-icon bg-primary text-center">
                    <i class="fa fa-clone"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{number_format($summary->get('points'))}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Points Earned</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>Recent Tickets</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                      <table class="table table-striped table-hover table-advance dt-responsive">
                          <thead>
                          <tr>
                              <th class="hidden-xs">Ticket #</th>
                              <th class="hidden-xs">Ticket Type</th>
                              <th>Holder Name</th>
                              <th>Event</th>
                              <th class="amount hidden-xs">Amount Paid</th>
                              <th></th>
                          </tr>
                          </thead>
                          <tbody>
                          @forelse($tickets as $ticket)
                              <tr>
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
                                      <a href="{{ route('app.tickets.download', $ticket->id) }}"
                                         class="btn btn-primary btn-xs"><i class="fa fa-download"></i> download </a>
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
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>Recent Orders</h3>
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
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="section-artist-content">
                @include('pages.partials.events_list_view')
            </div>
        </div>
    </div>

@endsection

@push('page_js')
<script type="text/javascript" src="{{ asset('plugins/fullcalendar/fullcalendar.min.js') }}"></script>
@endpush

@push('page_scripts')
<script>
    $(function () {
        $('#my_cal').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            defaultDate: moment(),
            navLinks: true, // can click day/week names to navigate views
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: App.MyEvents
        });
    })
</script>
@endpush
