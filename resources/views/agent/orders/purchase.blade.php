@extends('layouts.page')

<?php $maps = true ?>

@section('body_class','body-login')

@section('content')
    <div class="container">
        <div class="panel panel-default mt-30">
            <div class="panel-heading">Order Details: Attendees</div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-md-4 col-md-push-8">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <i class="ico-cart mr5"></i>
                                    Order Summary
                                </h3>
                            </div>

                            <div class="panel-body pt0">
                                <table class="table mb0 table-condensed">
                                    @foreach($reservations as $reservation)
                                        <tr>
                                            <td class="pl0">{{ $reservation['name'] }} X
                                                <b>{{ $reservation['quantity'] }}</b></td>
                                            <td style="text-align: right;">
                                                @if((int)ceil($reservation['unit_price']) === 0)
                                                    FREE
                                                @else
                                                    {{ money($reservation['unit_price']) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                            @if($total)
                                <div class="panel-footer">
                                    <h5>
                                        Total: <span style="float: right;"><b>{{ money($total) }}</b></span>
                                    </h5>
                                </div>
                            @endif

                        </div>
                        <div class="help-block">
                            Please note you only have <span id='countdown'></span> to complete this transaction before
                            your tickets are re-released.
                        </div>
                    </div>
                    <div class="col-md-8 col-md-pull-4">
                        <div class="content event_view_order">
                            <h3>Buyer Details</h3>
                            <div class="order_details well">
                                <div class="row">
                                    <div class="col-sm-4 col-xs-6">
                                        <b>First Name</b><br> {{$user->first_name}}
                                    </div>

                                    <div class="col-sm-4 col-xs-6">
                                        <b>Last Name</b><br> {{$user->last_name}}
                                    </div>

                                    <div class="col-sm-4 col-xs-6">
                                        <b>Phone</b><br> {{ $user->phone }}
                                    </div>

                                    <div class="col-sm-4 col-xs-6">
                                        <b>Email</b><br> {{$user->email}}
                                    </div>
                                </div>
                            </div>
                            <div class="p-10">
                                <a href="javascript:void(0);" class="btn btn-primary btn-xs" id="txo_cp">
                                    Copy buyer details to all ticket holders
                                </a>
                            </div>
                        </div>
                        <h4>Enter attendee details below</h4>
                        <form method="post" action="">
                            {!! csrf_field() !!}

                            <?php $order_count = 1; ?>
                            @foreach($reservations as $reservation)
                                @for($i = 1; $i <= $reservation['quantity']; $i++)
                                    @include('pages.forms.attendee_form', [
                                        'ticket_id' => $reservation['ticket_id'],
                                        'ticket_name' => $reservation['name'],
                                        'ticket_count' => $i,
                                        'order_count' => $order_count++
                                    ])
                                @endfor
                            @endforeach
                            @if($code = $organiser->is_agent($event->id))
                                <input type="hidden" value="{{ $code }}" name="sales_person_code">
                            @else
                                <fieldset>
                                    <legend>Sales Person Ref</legend>
                                    <div class="form-group {{$errors->has('sales_person_code')? 'has-error': ''}}">
                                        {!! Form::label('sales_person_code','Enter sales person code:') !!}
                                        {!! Form::text('sales_person_code',null,['class' => 'form-control']) !!}
                                        {!! $errors->first('sales_person_code', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </fieldset>
                            @endif
                            <div class="">
                                <button type="submit" class="btn btn-primary btn-block"> Proceed to Checkout</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
<script>
    $(function () {
        $('#txo_cp').click(function (e) {
            const user = App.User;
            $('.txo_fn').val(user.first_name);
            $('.txo_ln').val(user.last_name);
            $('.txo_e').val(user.email);
            $('.txo_p').val(user.phone);
        })
    })
</script>
@endpush
