@extends('layouts.admin')

<?php $vue = true ?>
<?php $maps = true ?>
<?php $charts = true ?>

@section('page')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-success" data-step="1" data-intro="{{ trans('intro.user.total_paid') }}" data-position='right'>
                <div class="card-icon bg-success text-center">
                    <i class="fa fa-money"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{money($event->sales_volume)}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Sales Volume</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-danger" data-step="2" data-intro="{{ trans('intro.user.total_arrears') }}" data-position='right'>
                <div class="card-icon bg-danger text-center">
                    <i class="fa fa-ticket "></i>
                </div>

                <div class="card-block">
                    <div class="h5"> {{number_format($event->tickets_sold)}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Tickets Sold</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="card bd-primary" data-step="3" data-intro="{{ trans('intro.user.total_services') }}" data-position='right'>
                <div class="card-icon bg-primary text-center">
                    <i class="fa fa-eye"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{number_format(0,0)}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">Reach</div>
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
        <div class="col-md-6 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="count" class="chart-container"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="tickets" class="chart-container"></div>
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
                categories: get_data(App.EventChartData.chartData, 'date'),
                crosshair: true
            },
            series: [
                {
                    name: 'Sales Volume',
                    data: get_data(App.EventChartData.chartData, 'sales_volume')
                }
            ]

        });

        Highcharts.chart('count', {

            title: {
                text: 'Total tickets sold in the Last 20 Days'
            },
            yAxis: {
                title: {
                    text: 'No. Of Tickets'
                }
            },
            xAxis: {
                categories: get_data(App.EventChartData.chartData, 'date'),
                crosshair: true
            },
            series: [
                {
                    name: 'No Of Tickets',
                    data: get_data(App.EventChartData.chartData, 'tickets_sold')
                }
            ]

        });
        Highcharts.chart('tickets', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Tickets Sold by Type'
            },
            tooltip: {
                pointFormat: 'No. {point.y} <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Tickets',
                colorByPoint: true,
                data: get_ticket_summary_data()
            }]
        });
    })

    function get_ticket_summary_data() {
        data = [];
        App.EventChartData.ticketData.forEach(function (item) {
            data.push({ name: item.label, y: item.value})
        })
        return data;
    }
</script>
@endpush
