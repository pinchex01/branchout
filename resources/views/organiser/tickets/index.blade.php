@extends('layouts.organiser')

<?php $vue = true ?>
<?php $event_menu = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Manage Tickets Types
            <div class="pull-right">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#md_ticket_frm"><i class="fa fa-cart-plus"></i> Add</button>
                @push('o_modals')
                <div class="modal fade" id="md_ticket_frm"  role="basic" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"> Create Order </h4>
                                    </div>
                                    <div class="modal-body">
                                        <ticket-form event="{{ $event->id }}"></ticket-form>
                                    </div>
                                </div>
                            </div>
                        </div>
                 @endpush
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-advance dt-responsive">
                    <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Price</th>
                        <th>Sold</th>
                        <th>Remaining</th>
                        <th>Revenue</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>
                                <strong>{{$ticket->name}}</strong>
                            </td>
                            <td>{{$ticket->price}}</td>
                            <td>{{$ticket->quantity_sold}}</td>
                            <td>{{$ticket->quantity_available ? 'Unlimited' : $ticket->quantity_remaining }}</td>
                            <td>{{$ticket->sales_volume}}</td>
                            <td>
                                <?php $now  = \Carbon\Carbon::now() ?>
                                @if($ticket->on_sale_date->format("Y-m-d") <= $now->format("Y-m-d"))
                                    <span class="text-center text-primary">On-Sale</span>
                                @elseif($ticket->end_sale_date->format('Y-m-d') < $now->format('Y-m-d'))
                                    <span class="text-center text-danger">Sales have ended</span>
                                @else
                                        <span class="text-center text-danger">Sale begins on {{$ticket->on_sale_date->format("Y-m-d")}}</span>
                                @endif
                            </td>
                            <td>
                                <a  href="{{ route('organiser.tickets.edit',[$organiser->slug, $event->id, $ticket->id]) }}"
                                        class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <p class="text-center">
                                    No tickets added yet. <br>
                                    <a href="#md_ticket_frm" data-toggle="modal" class="btn btn-primary btn-sm" ><i class="fa fa-ticket"></i> Add Ticket </a>
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

