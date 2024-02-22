@extends('layouts.page')

<?php $vue = true ?>

@section('body_class','body-login')

@section('content')
    <div class="container">
        <div class="row hidden-xs">

            <div class="ssl-container col-xs-12 col-sm-4 col-lg-3 hidden-xs">
                <span class="ssl glyphicon glyphicon-lock"></span>
                <span class="ssl-text">
                256 Bit SSL<br />
                Secure
            </span>
                <div class="clearfix"></div>
            </div>

            <div class="col-xs-12 col-sm-8 col-lg-9">
                <div class="row">
                    <div class="col-xs-4 col-sm-4">
                        <div class="step">
                            <i class="fa fa-calendar text-success"></i>
                            <span class="hidden-xs">Pick Event</span>
                            <div class="hidden-xs caret right"></div>
                            <div class="visible-xs caret bottom"></div>
                        </div>
                    </div>

                    <div class="col-xs-4 col-sm-4">
                        <div class="step active">
                            <span class="fa fa-user-plus"></span>
                            <span class="hidden-xs">Ticket Details</span>
                            <div class="hidden-xs caret right"></div>
                            <div class="visible-xs caret bottom"></div>
                        </div>
                    </div>

                    <div class="col-xs-4 col-sm-4">
                        <div class="step">
                            <span class="fa fa-money"></span>
                            <span class="hidden-xs">Payment</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="col-xs-12" />
        </div>
        <div class="panel panel-default mt-30">
            <div class="panel-heading">Ticket Details</div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-md-4 col-md-push-8">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <i class="ico-cart mr5"></i>
                                    Purchase Summary
                                </h3>
                            </div>

                            <div class="panel-body pt0">
                                <table class="table mb0 table-condensed">
                                    @foreach($reservations as $reservation)
                                        <tr>
                                            <td class="pl0">{{ $reservation['name'] }} X <b>{{ $reservation['quantity'] }}</b></td>
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
                            Please note you only have <span id='countdown'></span> to complete this transaction before your tickets are re-released.
                        </div>
                    </div>
                    <div class="col-md-8 col-md-pull-4">
                        @include('partials.notifier')
                        <order-details-form cart_id="{{ $cart_id }}"></order-details-form>
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
