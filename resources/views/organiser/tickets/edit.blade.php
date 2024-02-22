@extends('layouts.organiser')

<?php $vue = true ?>
<?php $maps = true ?>
<?php $events_menu = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Edit Event Ticket
            <div class="pull-right">
                <a href="{{ route('organiser.tickets.index', [$organiser->slug, $event->id]) }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <ticket-form event="{{ $event->id }}" action="edit"></ticket-form>
        </div>
    </div>
@endsection
