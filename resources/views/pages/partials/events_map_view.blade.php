@push('page_css')
<style>
    #wrapper {
        margin: 0 auto;
        max-width: 100%;
    }


    #evt_m {
        height: 800px;
        width: 100%;
    }

</style>
@endpush
<div id="wrapper">
    <div id="evt_m"></div>
</div>

</div>

@push('page_js')
<script type="text/javascript" src="{{ asset('plugins/jquery.mapit.min.js') }}"></script>
@endpush

@push('page_scripts')
<script>
    $(function () {
        var map  = $('#evt_m').mapit({
            latitude:    37.970996,
            longitude:   23.730542,
            zoom:        16,
            type:        'ROADMAP',
            scrollwheel: false,
            markers: [

                {
                    latitude:   35.970996,
                    longitude:  20.730542,
                    icon:       'images/marker_red.png',
                    title:      'The Hotel',
                    open:       false,
                    center:     true
                },
                {
                    latitude:   37.970996,
                    longitude:  23.730542,
                    icon:       'images/marker_red.png',
                    title:      'The Hotel',
                    open:       false,
                    center:     true
                },
                {
                    latitude:   35.970996,
                    longitude:  20.730542,
                    icon:       'images/marker_red.png',
                    title:      'The Hotel',
                    open:       false,
                    center:     true
                }
            ],
            address: '<h2>The Hotel</h2><p>Address 1, Area - County<br />Athens 123 45, Greece</p><p>Tel.: +30 210 123 4567<br />Fax: +30 210 123 4567</p>',

            origins: [
                ['37.936294', '23.947394'],
                ['37.975669', '23.733868']
            ]
        });
    })
</script>
@endpush