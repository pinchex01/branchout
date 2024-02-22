@extends('layouts.page')

<?php
$layout  = request()->get('layout','list');

if ($layout  == 'map')
    $maps  = false;
?>
@section('body_class','body-landing')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <section class="section-upcoming-events">
                    <div class="row">
                        <div class="section-header">
                            <h2>Upcoming Events</h2>
                            <p>Find events and buy tickets online. Its easy and fast</p>
                        </div>
                    </div>
                </section>
                <div class="pull-right text-right">
                    <a href="#c_search" data-toggle="collapse" class="btn"><i class="fa fa-search"></i> Search Event </a>
                    <a href="?layout=list" class="btn"><i class="fa fa-th-list"></i> </a>
                    <a href="?layout=grid" class="btn"><i class="fa fa-th-large"></i> </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-md-12" id="property-listings">
            <form id="c_search" class="collapse {{ old('q') ? 'in' :'' }} mt-20">
                <div class="panel panel-default">
                    <div class="panel-body pt-20">
                        <div class="form-group">
                            <div class="col-md-11">
                                <div class="input-group stylish-input-group">
                                    <input type="text" name="q" v-model="q" class="form-control"  value="{{ old('q') }}" placeholder="Keywords e.g. Party People Live 2017" >
                                    <span class="input-group-addon">
                                    <button type="submit">
                                        <span class="fa fa-search"></span>
                                    </button>
                                </span>
                                </div>

                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-danger"><i class="fa fa-filter"></i> Search </button>
                            </div>

                        </div>

                    </div>
                </div>
            </form>
            <div class="section-artist-content">
                @if($layout == 'list')
                    @include('pages.partials.events_list_view')
                @elseif($layout  == 'grid')
                    @include('pages.partials.events_grid_view')
                @elseif($layout == 'map')
                    @include('pages.partials.events_map_view')
                @else
                    @include('pages.partials.events_list_view')
                @endif

                @if(!$events->count())
                    <h3 class="text-center">No results found matching <i>{{ old('q') }}</i></h3>
                @endif
            </div>
        </div>
    </div>
@endsection