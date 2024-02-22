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
            <event-form action="edit"></event-form>
        </div>
    </div>
@endsection


@push('page_js')
<script src="{{asset('plugins/locationpicker.jquery.min.js')}}" type="text/javascript"></script>

@endpush

@push('page_scripts')
<script>

    $(function () {

        var lat = App.Event.lat? App.Event.lat :  -1.283333;
        var lon = App.Event.lng? App.Event.lng:  36.8166667;
        $('#map-component').locationpicker({
            // location: {latitude: -1.2833333, longitude: 36.8166667},
            location: {
                latitude: lat,
                longitude: lon
            },
            locationName: "",
            radius: 10,
            zoom: 15,
            scrollwheel: true,
            inputBinding: {
                latitudeInput: $('#us2-lat'),
                longitudeInput: $('#us2-lon'),
                radiusInput: $('#us2-radius'),
                locationNameInput: $('#us2-address')
            },
            enableAutocomplete: true,
            enableReverseGeocode: true,

        });

    });
</script>
@endpush
