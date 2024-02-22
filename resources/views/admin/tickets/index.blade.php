@extends('layouts.organiser')

<?php $vue = true ?>
<?php $maps = true ?>
<?php $event_menu = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Manage Event Tickets
            <div class="pull-right">
                <button href="#md_ticket_frm" data-toggle="modal" class="btn btn-primary btn-sm" ><i class="fa fa-ticket"></i> Add Ticket </button>
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
                                <strong>{{$ticket->name}}</strong> <br>
                                {!! $ticket->description !!}
                            </td>
                            <td>{{$ticket->price}}</td>
                            <td>{{$ticket->quantity_sold}}</td>
                            <td>{{$ticket->quantity_available ? 'Unlimited' : $ticket->quantity_remaining }}</td>
                            <td>{{$ticket->sales_volume}}</td>
                            <td>{{$ticket->status}}</td>
                            <td><a href="#" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a> </td>
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

@push('modals')
<div class="modal fade" id="md_ticket_frm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Create Ticket</h4>
            </div>
            <div class="modal-body">
                <ticket-form event="{{ $event->id }}"></ticket-form>
            </div>
        </div>
    </div>
</div>
@endpush
