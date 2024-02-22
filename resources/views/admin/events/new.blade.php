@extends('layouts.organiser')

<?php $vue = true ?>
<?php $maps = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Add Event
            <div class="pull-right">
                <a href="{{ route('organiser.events.index', [$organiser->slug]) }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back to Events </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <event-form></event-form>
        </div>
    </div>
@endsection
