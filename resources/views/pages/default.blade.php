@extends('layouts.home')

<?php
$layout  = request()->get('layout','list');

if ($layout  == 'map')
    $maps  = false;
?>
@section('body_class','body-landing body_padding_top')

@section('content')

<div class="row">

<div class="container-fluid banner_container">


    <div class="">
        <div class="banner">

            <div id="myCarousel" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <!-- <ol class="carousel-indicators">
                 <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                 <li data-target="#myCarousel" data-slide-to="1"></li>
                 <li data-target="#myCarousel" data-slide-to="2"></li>
                 <li data-target="#myCarousel" data-slide-to="3"></li>
               </ol>  -->

                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                    <div class="item active">
                        <img src="{{ asset('img/wizkid/wizkid-main.jpg') }}" class="slide-img">
                    </div>
                    
                </div>

                <!-- Left and right controls -->
                <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>

</div>

</div>


        <div class="container">
        <section class="section-upcoming-events mt-40  mb-10">
                    <div class="row">
                        <div class="section-header">
                            <h2>Featured Events</h2>
                            <p>Find events and buy tickets online. Its easy and fast</p>
                            <a href="{{ route('app.events.list') }}">See all events</a>
                        </div>
                    </div>
                </section>

        <div id="property-listings">
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

      <div class="col-md-12 mb-10">
            <section class="section-upcoming-events">
                    <div class="section-header">
                        <h2>Official Hospitality Partner</h2>
                        <p>dusitD2 Nairobi is a vibrant city hotel retreat where style, entertainment and art converge</p>
                    </div>
            </section>
        </div>


        <div class="row">

        <div class="section-artist-content">
          <div class="artist-event-item">
            <div class="col-md-4">
                <a class="thumbnail" href="http://www.d2nairobi.com/" target="_blank">
                    <img src="{{ asset('images/dusit.jpg') }}">
                </a>
            </div>
            <div class="col-md-4">
                <a class="thumbnail" href="http://www.d2nairobi.com/" target="_blank">
                    <img src="{{ asset('images/dusit2.jpg') }}">
                </a>
            </div>
            <div class="col-md-4">
                <a class="thumbnail" href="http://www.d2nairobi.com/" target="_blank">
                    <img src="{{ asset('images/dusit3.jpg') }}">
                </a>
            </div>
            
          </div>
          
        </div>


      </div>
    </div>


@endsection
