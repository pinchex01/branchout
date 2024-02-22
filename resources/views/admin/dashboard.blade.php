@extends('layouts.admin')

<?php $charts = true ?>

@section('page')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-success" data-step="1" data-intro="{{ trans('intro.user.total_paid') }}" data-position='right'>
                <div class="card-icon bg-success text-center">
                    <i class="fa fa-calendar-plus-o"></i>
                </div>

                <div class="card-block">
                    <div class="h5">KES. {{number_format(0)}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Events</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-danger" data-step="2" data-intro="{{ trans('intro.user.total_arrears') }}" data-position='right'>
                <div class="card-icon bg-danger text-center">
                    <i class="fa fa-calendar-times-o "></i>
                </div>

                <div class="card-block">
                    <div class="h5">KES. {{number_format(0,2)}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Tickets Sold</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="card bd-primary" data-step="3" data-intro="{{ trans('intro.user.total_services') }}" data-position='right'>
                <div class="card-icon bg-primary text-center">
                    <i class="fa fa-clone"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{number_format(0,0)}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Sales Volume</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
      <div class="col-md-12">
          <div class="panel panel-default">
              <div class="panel-body">
                  <div id="sales" class="chart-container"></div>
              </div>
          </div>
      </div>
    </div>

@endsection


@push('page_scripts')
<script>
    $(function () {

      Highcharts.chart('sales', {

          title: {
              text: 'Ticket Sales for the Last 20 Days'
          },

          yAxis: {
              title: {
                  text: 'Sales Volume'
              }
          },
          xAxis: {
              categories: get_data(App.SalesChartData.chartData, 'date'),
              crosshair: true
          },
          series: [
              {
                  name: 'Sales Volume',
                  data: get_data(App.SalesChartData.chartData, 'sales_volume')
              }
          ]

      });
    })
</script>
@endpush
