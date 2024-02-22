<template>
    <form action="" method="post"  v-loading.body="loading" v-on:submit="validate">
        <input type="hidden" name="_token" v-model="crsfToken"/>
        <div v-if="!authed">
            <div class="row">
                    <div class="col-sm-6 col">
                        <div class="form-group" v-bind:class="{ 'has-error' :  errors.has('first_name') || errors.first_name }">
                            <label class="control-label">First Name</label>
                            <input type="text" name="first_name" v-model="order.first_name" class="form-control" v-validate="'required'" required>
                            <form-error v-if=" errors.has('first_name') || errors.first_name " :errors="errors">
                                {{ errors.first('first_name') }}
                            </form-error>
                        </div>
                    </div>
                    <div class="col-sm-6 col">
                        <div class="form-group" v-bind:class="{ 'has-error' :  errors.has('last_name') || errors.last_name }" style="padding-right: 0 !important;">
                            <label class="control-label">Last Name</label>
                            <input type="text" name="last_name" v-model="order.last_name" class="form-control" v-validate="'required'" required>
                            <form-error v-if=" errors.has('last_name') || errors.last_name " :errors="errors">
                                {{ errors.first('last_name') }}
                            </form-error>
                        </div>
                    </div>
                </div>
                <div class="form-group" v-bind:class="{ 'has-error' : errors.has('email') || errors.email }">
                    <label class="control-label">Email</label>
                    <input type="email" name="email" v-model="order.email" class="form-control" v-validate="'required'" required>
                    <form-error v-if="errors.has('email') || errors.email " :errors="errors">
                        {{ errors.first('email') }}
                    </form-error>
                </div>
                <div class="form-group" v-bind:class="{'has-error': errors.has('phone') || errors.phone }">
                    <phone-input-field label="Phone Number"></phone-input-field>
                    <input type="hidden" name="phone" v-model="order.phone" v-validate="'required'">
                    <form-error v-if="errors.has('phone') || errors.phone" :errors="errors">
                        {{ errors.first('phone') }}
                    </form-error>
                </div>
        </div>
        <div v-if="authed">
            <input type="hidden" name="first_name" v-model="order.first_name"> 
            <input type="hidden" name="last_name" v-model="order.last_name"> 
            <input type="hidden" name="email" v-model="order.email"> 
            <input type="hidden" name="phone" v-model="order.phone"> 
        </div>

        <div class="" id="guests">

            <h3>Enter ticket details below</h3>
            <div class="p-10">
                <a href="javascript:void(0);" class="btn btn-primary btn-xs" v-on:click="copy_buyer_details()">
                    Copy buyer details to all ticket holders
                </a>
            </div>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>Ticket</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in this.get_attendees()">
                    <td>{{ item.pk }}
                        <input type="hidden" :name="'attendee[' + item.pk +'][ticket_id]'" v-model="attendees[item.pk].ticket_id" required>
                    </td>
                    <td>{{ item.name }}</td>
                    <td>
                        <div class="form-group" v-bind:class="{ 'has-error' : errors.has('attendees.' + item.pk + '.first_name') || errors['attendees.' + item.pk + '.first_name']}">
                            <input type="text" :name="'attendee[' + item.pk +'][first_name]'" v-model="attendees[item.pk].first_name" class="form-control txo_fn" v-validate="'required'" placeholder="First Name" required>
                            <form-error v-if="errors.has('attendees.' + item.pk + '.first_name') || errors['attendees.' + item.pk + '.first_name']" :errors="errors">
                                {{ errors.first('attendees.' + item.pk + '.first_name') }}
                            </form-error>
                        </div>
                    </td>
                    <td>
                        <div class="form-group" v-bind:class="{ 'has-error' : errors.has('attendees.' + item.pk + '.last_name') || errors['attendees.' + item.pk + '.last_name']}">
                            <input type="text" :name="'attendee[' + item.pk +'][last_name]'"  v-model="attendees[item.pk].last_name" class="form-control txo_ln" v-validate="'required'" placeholder="Last Name" required>
                            <form-error v-if="errors.has('attendees.' + item.pk + '.last_name') || errors['attendees.' + item.pk + '.last_name']" :errors="errors">
                                {{ errors.first('attendees.' + item.pk + '.last_name') }}
                            </form-error>
                        </div>
                    </td>

                </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <div class="pull-left">

            </div>
            <vue-simple-spinner v-if="loading"> Thinking </vue-simple-spinner>
            <button type="submit" class="btn btn-primary" :disabled="loading"> Continue</button>
        </div>
    </form>
</template>
<script>

    import Helpers from '../helpers'
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";
    import Countdown from 'vuejs-countdown'
    export default {
        props: [ "card_id"],
        components: {VueSimpleSpinner, Countdown},
        data(){
            return {
                step: 1,
                all_tickets: App.Tickets,
                tickets: [],
                order: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    expires_at: null,
                    id_number: null
                },
                costs: {},
                sub_total: 0,
                coupon: 0,
                discount: 0,
                total: 0,
                reservations : [],
                loading: false,
                attendees: [],
                payment: {},
                config: null ,
                payment_option: 'mpesa',
                sales_agent_code: this.agent_code,
                user: App.User,
                s_errors: [],
                buying_error: null,
                order_error: null,
                valid_user: false,
                crsfToken: window.Laravel.csrfToken,
                authed: !!App.User
            }
        },
        mounted() {
            this.$validator.attach('phone', 'required');
        },
        watch: {

        },
        methods: {

            get_ticket(ticket_id) {
                const tickets  = this.all_tickets;
                this.costs[ticket_id] = 0
                return tickets.find( (item) => {
                    return  item.id === ticket_id;
                })

            },

            /**
             *
             * @returns {Array}
             */
            set_attendees(){
                let attendees  = [];
                var count  = 1;
                for( var key in this.reservations) {
                    //reservation
                    if (this.reservations.hasOwnProperty(key)) {
                        let reservation  = this.reservations[key]
                        for (var i = 1; i <= parseInt(reservation.quantity * reservation.groups_of); i++) {
                            attendees[count] = {
                                pk: count, ticket_id: reservation.ticket_id, name: reservation.name,
                                first_name: null, last_name: null, email: null, phone: null, num: i
                            }
                            count++
                        }
                    }

                };

                this.attendees  =  attendees;
                return attendees;
            },
            get_attendees() {
                let attendees = [];
                for (var key in this.attendees){
                    if (this.attendees.hasOwnProperty(key)) {
                        let attendee  = this.attendees[key]
                        attendees.push(attendee)
                    }
                }
                return attendees;
            },
            get_valid_attendees(){
                let data  = {};
                for(var k in this.attendees){
                    if (this.attendees.hasOwnProperty(k)){
                        data[k] = this.attendees[k]
                    }
                }
                return data;
            },
            copy_buyer_details() {
                const user = this.order;
                for( var k in this.attendees){
                    if(this.attendees.hasOwnProperty(k)){
                        this.attendees[k].first_name = user.first_name
                        this.attendees[k].last_name = user.last_name
                        this.attendees[k].email = user.email
                        this.attendees[k].phone = user.phone
                    }
                }
            },
            get_order_expiry() {
                return moment(this.order.expires_at.date).format('YYYY-MM-DD HH:mm:ss');
            },
            validate() {
                this.$validator.validateAll()
                .then( () => {
                    this.$notify.success({
                        title: 'O',
                        message: 'All Good'
                    });
                })
                .catch( () => {
                    this.$notify.error({
                        title: 'Error',
                        message: 'This is an error message'
                    });
                    return;
                })
            }

        },
        created(){
            if(this.user){
                const user = this.user;
                this.order.first_name  = user.first_name,
                this.order.last_name  = user.last_name,
                this.order.email  = user.email,
                this.order.phone  = user.phone
            }

            //set order info
            this.reservations = App.OrderInfo.reservations
            this.set_attendees();

            bus.$on('int-phone-number',  (data) => {
                this.order.phone = data.full;
            })
        
        }
    }
</script>