@extends('layouts.user')

<?php $vue = true ?>
<?php $maps = true ?>
<?php $event_menu = true ?>

@section('page')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-success" data-step="1" data-intro="{{ trans('intro.user.total_paid') }}" data-position='right'>
                <div class="card-icon bg-success text-center">
                    <i class="fa fa-calendar-plus-o"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{ money($summary->sum()) }}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Total Sales Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-danger" data-step="2" data-intro="{{ trans('intro.user.total_arrears') }}" data-position='right'>
                <div class="card-icon bg-danger text-center">
                    <i class="fa fa-bank"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{ money($summary->get('settled',0)) }}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Total Paid</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="card bd-primary" data-step="3" data-intro="{{ trans('intro.user.total_services') }}" data-position='right'>
                <div class="card-icon bg-primary text-center">
                    <i class="fa fa-cart-plus"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{ money($summary->sum() - $summary->get('settled',0)) }}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Available Balance</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    My Sales
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-advance dt-responsive">
                            <thead>
                            <tr>
                                <th>Code #</th>
                                <th>Event</th>
                                <th>Tickets Sold</th>
                                <th>Total Sales</th>
                                <th>Commission</th>
                                <th>Paid</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td>{{ $sale->code }}</td>
                                    <td>{{$sale->event}}</td>
                                    <td>{{$sale->tickets_sold}}</td>
                                    <td>{{$sale->total }}</td>
                                    <td>{{$sale->fees}}</td>
                                    <td>
                                        @if($sale->settled)
                                            <span class="label label-success">Yes</span>
                                        @else
                                            <span class="label label-danger">No</span>
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <p class="text-center">
                                            You don't have a sales team yet. <br>
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
