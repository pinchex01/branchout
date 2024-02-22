@extends('layouts.organiser')

<?php $vue = true ?>
<?php $maps = true ?>
<?php $event_menu = true ?>

@section('page')
<div class="panel panel-default">
        <div class="panel-heading">
            Search Ticket
        </div>
        <div class="panel-body">
            <div  id="cs-filter">
                <form class="well">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Ticket No</label>
                            <div class="col-sm-10">
                                {!! Form::text('term', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Ticket No, Order No, Ticket Holder Name or Phone',
                                ]) !!}
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer filter-actions">
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-search"></i> Search
                        </button>
                    
                    </div>
                </form>
            </div>
            
        </div>
    </div>
    @if($attendee) 
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Ticket Details
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-hover">
                            <tbody>
                                <tr>
                                    <td>Ticket No.</td>
                                    <td>{{ $attendee->ref_no}}</td>
                                </tr>
                                <tr>
                                    <td>Holder</td>
                                    <td>{{ $attendee->full_name}}</td>
                                </tr>
                                <tr>
                                    <td>Ticket Type</td>
                                    <td>{{ $attendee->ticket->name}}</td>
                                </tr>
                                <tr>
                                    <td>Date Created</td>
                                    <td>{{ $attendee->created_at}}</td>
                                </tr>
                                <tr>
                                    <td>Check In Status</td>
                                    <td>{!! $attendee->get_status_label() !!}</td>
                                </tr>
                                <tr>
                                    <td>Checked In By</td>
                                    <td>
                                        @if($attendee->check_in_time) 
                                            {{ $attendee->user }} @ {{ $attendee->check_in_time }}
                                        @else
                                            <form method="post">
                                                {!! csrf_field() !!}
                                                <input type="hidden" name="ticket_no" value="{{ $attendee->ref_no}}">
                                                <button type="submit" class="btn btn-primary"> Check In</button>
                                            </form>
                                        @endif
                                    
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif(request()->has('term') && !$attendee)
            <div class="panel pane-default">
                <div class="panel-body">
                    <p class="text-center mt-20">No ticket found matching your search term</p>
                </div>
            </div>
            @endif

@endsection
