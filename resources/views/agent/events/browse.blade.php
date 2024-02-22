@extends('layouts.agent')

<?php $vue = true ?>
<?php $maps = true ?>
<?php
$layout  = request()->get('layout','list');

if ($layout  == 'map')
    $maps  = false;
?>

<?php $public_events = true ; ?>

@section('page')
    <div class="row">
        <div class="row">
            <div class="col-md-12">
                <h1>Upcoming Events</h1>
                <div class="pull-left">
                    <p>Find events and buy tickets online. Its easy and fast</p>
                </div>
                <div class="pull-right text-right">
                    <a href="?layout=list" class="btn"><i class="fa fa-th-list"></i> </a>
                    <a href="?layout=grid" class="btn"><i class="fa fa-th-large"></i> </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="col-md-12" id="property-listings">
            @if($layout == 'list')
                @include('agent.events.partials.events_list_view')
            @elseif($layout  == 'grid')
                @include('agent.events.partials.events_grid_view')
            @elseif($layout == 'map')
                @include('agent.events.partials.events_map_view')
            @else
                @include('agent.events.partials.events_list_view')
            @endif
        </div>
    </div>
@endsection
