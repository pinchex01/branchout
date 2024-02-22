@extends('layouts.pdf')

@section('title', "$event Attendees")

@section('body')
  <table class="table table-striped table-hover table-advance dt-responsive">
      <thead>
      <tr>
          <th>Ticket #</th>
          <th>Order #</th>
          <th>Name</th>
          <th>Ticket</th>
          <th>Check-in Date</th>
      </tr>
      </thead>
      <tbody>
      @forelse($items as $attendee)
          <tr>
              <td>{{ $attendee->ref_no }}</td>
              <td>{{ $attendee->order->ref_no }}</td>
              <td>{{$attendee->full_name}}</td>
              <td>{{ $attendee->ticket }}</td>
              <td>{{$attendee->check_in_date? : 'N/A'}}</td>
          </tr>
      @empty
          <tr>
              <td colspan="4">
                  <p class="text-center">
                      No attendees have been added yet. <br>
                  </p>
              </td>
          </tr>
      @endforelse
      </tbody>
  </table>
@stop
