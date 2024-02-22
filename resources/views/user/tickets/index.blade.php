@extends('layouts.user')

<?php $maps = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            My Tickets
            <div class="pull-right">
                <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#cs-filter" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter">
                        Filter</i></button>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
                <form class="well">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Ticket No</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[ticket_no]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Ticket No',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer filter-actions">
                        <button type="submit" class="btn btn-danger">
                            Filter
                        </button>
                        <a href="{{route('app.tickets.index')}}" class="btn btn-default">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
            <form  method="post" action="{{ route('app.tickets.batch_download') }}">
            <button type="submit" name="action" value="download" class="btn btn-info"><i class="fa fa-download"></i> Download Tickets </button>
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
                    @forelse($tickets as $ticket)
                        <tr>
                        <td><input type="checkbox" name="tickets[]" value="{{ $ticket->ref_no }}"></td>
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
                                @if(!$ticket->check_in_time) 
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
                {!! $tickets->render() !!}
            </div>
            </form>
            
        </div>
    </div>
@endsection
