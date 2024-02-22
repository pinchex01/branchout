@extends('layouts.organiser')

<?php $vue = true ?>
<?php $maps = true ?>
<?php $event_menu = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Attendees
            <div class="pull-right">
                <button class="btn btn-primary"> Invite Attendee</button>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-advance dt-responsive">
                    <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Ticket</th>
                        <th>Check-in Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($attendees as $attendee)
                        <tr>
                            <td>{{ $attendee->order->ref_no }}</td>
                            <td>{{$attendee->full_name}}</td>
                            <td>{{$attendee->email}}</td>
                            <td>{{$attendee->phone}}</td>
                            <td>{{$attendee->check_in_date? : 'N/A'}}</td>
                            <td>{{$attendee->status}}</td>
                            <td><a href="#" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </a> </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <p class="text-center">
                                    No attendees have been added yet. <br>
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