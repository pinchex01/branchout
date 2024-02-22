<template>
    <div>
        <form v-if="!paid"  method="post" action="" v-on:submit.prevent="submit">
            <div v-if="!user" class="note note-info">
                Login to enable other payment options
            </div>
            <alert-message v-if="alert.hasOwnProperty('message')" :type="alert.type">{{ alert.message }}</alert-message>
            <h4 class="mt-20">How do you wish to pay for this purchase?</h4>
            <div class="payment_options mt-10 m-b-30">
                <label><input type="radio" name="channel" value="mpesa" v-model="channel"> Default </label>
                <label v-if="user"><input type="radio" name="channel"  value="wallet" v-bind:disabled="parseFloat(user.wallet) < parseFloat(order.amount)" v-model="channel"> Wallet (Bal: {{ user.wallet}} ) </label>
                <label v-if="user"><input type="radio" name="channel" value="points" :disabled="parseFloat(user.party_points) < parseFloat(order.amount)" v-model="channel"> Points (Balance: {{ user.party_points}}) </label>

            </div>
            
            <fieldset v-if="channel  == 'mpesa'">
                <form id="moodleform" name="moodleform" method="post" :action="pesaflow.url" target="my_frame">
                    <input type="hidden" name="apiClientID" v-model="pesaflow.apiClientID" >
                    <input type="hidden" name="secureHash" v-model="pesaflow.secureHash" >
                    <input type="hidden" name="billDesc" v-model="pesaflow.billDesc" >
                    <input type="hidden" name="billRefNumber" v-model="pesaflow.billRefNumber" >
                    <input type="hidden" name="currency" v-model="pesaflow.currency" >
                    <input type="hidden" name="serviceID" v-model="pesaflow.serviceID" >
                    <input type="hidden" name="clientMSISDN" v-model="pesaflow.clientMSISDN" >
                    <input type="hidden" name="clientName" v-model="pesaflow.clientName" >
                    <input type="hidden" name="clientIDNumber" v-model="pesaflow.clientIDNumber" >
                    <input type="hidden" name="clientEmail" v-model="pesaflow.clientEmail" >
                    <input type="hidden" name="pictureURL" v-model="pesaflow.pictureURL" >
                    <input type="hidden" name="callBackURLOnSuccess" v-model="pesaflow.callBackURLOnSuccess" >
                    <input type="hidden" name="callBackURLOnFail" v-model="pesaflow.callBackURLOnFail" >
                    <input type="hidden" name="notificationURL" v-model="pesaflow.notificationURL" >
                    <input type="hidden" name="amountExpected" v-model="pesaflow.amountExpected" >
                </form>


                <iframe style="border: none;" scrolling="no" id="my_frame" width="100%" height="900px" name="my_frame" ></iframe>                
            </fieldset>
            <fieldset v-if="channel  == 'wallet'">
                <legend>Buy with Wallet</legend>
                <div class="well">
                    This option will use money available in your float to complete this purchase. If you are sure, click complete
                </div>
            </fieldset>
            <fieldset v-if="channel  == 'points'">
                <legend>Buy with Party Points</legend>
                <div class="well">
                    This option will redeem your Party Points to complete this purchase. If you are sure click Complete
                </div>
            </fieldset>
            <div class="modal-footer">
                <button class="btn btn-primary" :disabled="loading"> <i v-if="loading" class="fa-li fa fa-spinner fa-spin"></i> Complete Payment </button>
            </div>
        </form>
        <div v-if="paid" class="row">
            <div class="col-md-12 order_header">
                <div class="text-center">
                    <i class="fa fa-check-circle fa-6x text-success"></i>
                </div>
                <h1>Thank you for your order!</h1>

                
            </div>
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Order Summary
                    </h3>
                </div>

                <div class="panel-body pt0">
                    <table class="table mb0 table-condensed">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in order.order_items">
                                <td class="pl0">{{ item.ticket.name }}</td>
                                <td>{{ item.ticket.price }}</td>
                                <td> x {{ item.quantity }}</td>
                                <td style="text-align: right;">
                                    {{ item.total  > 0 ? item.total : 'FREE' }}
                                </td>
                            </tr>
                        </tbody>
                    
                        <tfoot>
                            <th colspan="3" style="text-align: right;">NET TOTAL</th>
                            <th style="text-align: right;"><b>{{ order.amount == 0 ? 'FREE' : order.amount}}</b></th>
                        </tfoot>
                    
                    </table>
                </div>

                    <div class="panel-footer">
                      <a href="/tickets" class="btn btn-link"> My Tickets</a>  
                    
                    </div>
                    <p>Your order is being processed, once complete you will receive an SMS on your phone</p>
            </div>
        </div>
    </div>
</template>
<script>
    import Helpers from '../helpers'
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";
    export default {
        props: [ "account_balance", "paybill_no" ],
        components: {
            VueSimpleSpinner},
        mounted() {
            this.user = App.User;
            this.paid  = App.Order.status  == 'paid'
        },
        data(){
            return {
                order: App.Order,
                payment: {},
                channel: 'mpesa',
                user: App.User ? App.User : null,
                alert: {},
                loading: false,
                paid: false,
                pesaflow: App.frmCheckout
            }
        },
        watch: {
            channel(value){
                if(value  == 'mpesa'){
                    this.load_iframe();
                }
                    
            }
        },
        methods: {
            submit() {
                this.loading = true;
                this.alert = {};
                var url  = '/api/checkout';
                axios.post(url, {
                    channel: this.channel,
                    order_ref: this.order.pk
                }).then( (response) => {
                    this.paid = true;
                    this.loading = false;
                }).catch( (error) => {
                    let data  = error.response.data;
                    this.alert = { type: 'danger', message: data.message}
                    this.loading = false;
                })
            },
            load_iframe() {
                let $form  = document.getElementById('moodleform');
                $form.submit();
            }
        },
        created(){
           if(App.User){
               Object.assign(this.user, App.User)
           }

           if(this.channel  == 'mpesa'){
                    this.load_iframe();
                }
        }
    }
</script>