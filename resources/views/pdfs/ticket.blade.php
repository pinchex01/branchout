<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Party People Ticket</title>
    <style type="text/css" media="screen, print">



        body {
            -webkit-print-color-adjust: exact !important;
        }

        @page  {
            size: A4;
            margin: 0;
        }

        @media  print {
            html, body {
                width: 210mm;
                height: 297mm;
            }
        }

        * {
            font-family: sans-serif;
        }

        ul {
            padding: 0;
            margin: 0;
        }

        /* sixth example */
        .equal-height-container {
            display: flex;
            width: 780px;
        }

        /* Main ticket */

        .main-ticket {
            background-color: #FFF;
            flex: 2;
            display: flex;
            flex-direction: column;
            background-image: url({{ asset('img/box.jpg') }});
            background-repeat: repeat;
            border: 1px dashed #25176a;
        }

        /* main-ticket Layers */

        .layer-1 {
            background-color: #25176a;
            flex: 1;
            display: table;
            width: 780px;
            -webkit-print-color-adjust: exact !important;
            border-bottom: 1px solid #25176a;
        }

        .layer-2 {
            background-color: transparent;
            flex: 1;
            border-right: 1px solid rgba(37, 23, 106, 0.6);

        }

        .layer-3 {
            background-color: rgba(37, 23, 106, 0.6);
            border-top: 1px solid rgba(37, 23, 106, 0.6);
            border-bottom: 1px solid rgba(37, 23, 106, 0.6);
            flex: 1;
            border-right: 1px solid rgba(37, 23, 106, 0.6);

        }

        .layer-11 {
        }

        .layer-4 {
            background-color: rgba(37, 23, 106, 0.1);
            border-bottom: 1px solid rgba(37, 23, 106, 0.1);
            flex: 1;
            border-right: 1px solid rgba(37, 23, 106, 0.6);

        }

        .layer-5 {
            background-color: transparent;
            flex: 1;
        }

        .layer-1 ul {
            vertical-align: middle;
        }

        .layer-1 ul li {
            color: #fff;

            flex: 1;
            vertical-align: middle;
            text-align: center;
            height: 70px;
            width: 260px;
            display: table-cell;
            text-transform: uppercase;
            font-family: sans-serif;
            font-size: 16px;
        }

        .layer-1 ul li:last-child {
            text-align: right;
            padding-right: 18px;
        }

        .layer-1 ul li:first-child {
            text-align: left;
            padding-left: 20px;
        }

        .layer-2 ul li {
            color: #25176a;
            flex: 1;
            vertical-align: middle;
            text-align: left;
            height: 84px;
            width: 308px;
            display: table-cell;
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 400;


        }

        .layer-2 ul li label {
            padding: 0;
            margin: 0;
            text-transform: uppercase;
        }

        .layer-2 ul li h3 {
            padding: 0;
            margin: 0;
            font-size: 20px;
            font-weight: 400;

        }

        .layer-2 ul li:first-child {
            padding-left: 20px;
        }

        .layer-3 ul li {
            color: #fff;
            font-family: sans-serif;
            flex: 1;
            vertical-align: middle;
            text-align: left;
            height: 40px;
            width: 157px;
            display: table-cell;
            font-family: sans-serif;
            font-size: 14px;
            text-transform: uppercase;

        }

        .layer-3 ul li:first-child {
            padding-left: 20px;
        }

        .layer-3 ul li:last-child {
            text-align: right;
            padding-right: 20px;
            width: 126px;
        }

        .layer-4 ul li {

            flex: 1;
            vertical-align: middle;
            text-align: left;
            height: 44px;
            width: 158px;
            display: table-cell;
            /* font-family: 'droidigaregular'; */
            text-transform: uppercase;

        }

        .layer-4 ul li:first-child {
            padding-left: 20px;
        }

        .layer-4 ul li:last-child {
            text-align: right;
            padding-right: 15px;
            width: 126px;
        }

        .layer-4 ul li h3 {
            padding: 20px 0;
            margin: 0;
            font-size: 25px;
            color: #25176a;
            font-weight: 400;
        }

        .layer-5 ul li {

            flex: 1;
            vertical-align: top;
            text-align: left;
            height: 54px;
            width: 390px;
            display: table-cell;
            /* font-family: 'droidigaregular'; */
            text-transform: uppercase;
            padding-top: 10px;
        }

        .layer-5 ul li:first-child {
            padding-left: 20px;
        }

        .layer-5 ul li:last-child {
            padding: 10px;
            text-align: right;
        }

        .layer-5 ul li label {
            font-size: 14px;
        }
    </style>
</head>

<body style="padding:20px">

<div class="equal-height-container">
    <div class="main-ticket">
        <div class="layer-1">
            <ul>
                <li><img src="http://partypeoplecore.dev/img/logo-purple.jpeg" height="50px"></li>
                <li>Event Ticket</li>
                <li>{{ $event->start_date->format('F d, Y') }}</li>
            </ul>
        </div>
        <div class="level-prime" style="display: table;vertical-align: top;">
            <div class="level-11" style="width: 640px;display: table-cell;vertical-align: top;">
                <div class="layer-2">
                    <ul>
                        <li>
                            <label>Show/Concert</label>
                            <h3>{{ $event->name }}</h3>
                        </li>
                        <li>
                            <label>Location</label>
                            <h3>{{ $event->location }}</h3>
                        </li>
                    </ul>
                </div>
                <div class="layer-3">
                    <ul>
                        <li>Serial</li>
                        <li>Section</li>
                        <li>Cost - KES</li>
                        <li>Time</li>
                    </ul>
                </div>
                <div class="layer-4">
                    <ul>
                        <li>
                            <h3>No. <br> {{ $attendee->ref_no }}</h3>
                        </li>
                        <li>
                            <h3>N/A <br> {{ $ticket->name }}</h3>
                        </li>
                        <li>
                            <h3>KES <br> {{ $ticket->price ? number_format($ticket->price) : 'FREE' }}</h3>
                        </li>
                        <li>
                            <h3> {{ $event->start_date->format('A') }} <br> {{ $event->start_date->format('H:i') }} </h3>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="level-12" style="width: 126px;display: table-cell;padding: 10px;">
                <?xml version="1.0" standalone="no"?>
                <!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
                    {!! DNS2D::getBarcodeSVG($attendee->ref_no, "QRCODE", 6, 6) !!}

                <label style="margin-top:10px;float:left;font-size: 14px;">{{ $attendee->full_name }}<br> ({{$order->user->id_number}})</label>
            </div>
        </div>

    </div>
</div>
</body>
</html>
