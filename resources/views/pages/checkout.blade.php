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
            <div class="panel-body">

                <div class="row">
                    <div class="col-md-12">
                        @include('partials.notifier')
                        <checkout-form @if(user()) account_balance="{{ user()->account->balance }}" @endif paybill_no="{{ settings('paybill_no', 759457 ) }}"></checkout-form>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_js') 
<script>
    $(function(){
        let $form  = document.getElementById('moodleform');
        $form.submit();
    })
</script>
@endpush
