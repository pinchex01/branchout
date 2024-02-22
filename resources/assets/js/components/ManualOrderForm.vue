<template>
    <form v-loading.body="loading" action="" method="post" v-on:submit.prevent="submit()">
        <alert-message v-if="alert.hasOwnProperty('message')" :type="alert.type">{{ alert.message }}</alert-message>
        <user-search-form></user-search-form>
        <input type="hidden" name="first_name" v-model="order.first_name">
        <input type="hidden" name="last_name" v-model="order.last_name">
        <input type="hidden" name="email" v-model="order.email">
        <input type="hidden" name="phone" v-model="order.phone">
        <input type="hidden" name="id_number" v-model="order.id_number">

        <div v-if="!!order.id_number" >
            <div v-if="!m_event_id" class="form-group" v-bind:class="{ 'has-error' : errors.event_id}">
                <label class="control-label">Select Event</label>
                <select name="event_id" v-model="event_id" class="form-control" v-validate="'required'">
                    <option value="">Selct Event</option>
                    <option v-for="event in events" v-bind:value="event.id">
                    {{ event.name }}
                    </option>
                </select>
                <form-error v-if="errors.event_id" :errors="errors">
                    {{ errors.event_id }}
                </form-error>
            </div>
            <div class="form-group" v-bind:class="{ 'has-error' : errors.ticket_id}">
                <label class="control-label">Ticket Type</label>

                <select name="ticket_id" v-model="ticket_id" class="form-control" v-validate="'required'">
                    <option value="">Choose Ticket</option>
                    <option v-for="ticket in tickets" v-bind:value="ticket.id">
                    {{ ticket.name + ' @ '+ticket.price }}
                    </option>
                </select>
                <form-error v-if="errors.ticket_id" :errors="errors">
                    {{ errors.ticket_id }}
                </form-error>
            </div>

              <div class="form-group" v-bind:class="{'has-error' : errors.has('terms') || errors.terms}">
                  <el-radio class="radio"  v-model="payment" label="full"> Fully Paid</el-radio >
                  <el-radio class="radio"  v-model="payment" label="discounted">Discounted</el-radio >
                  <el-radio class="radio"  v-model="payment" label="free"> Free</el-radio >
                  <form-error v-if="errors.has('terms') || errors.terms" :errors="errors">
                      {{ errors.first('terms') }}
                  </form-error>
              </div>
              <div v-if="payment == 'discounted'" class="form-group" v-bind:class="{'has-error': errors.has('discount') }">
                  <label class="control-label">Discount</label>
                  <input type="number" name="discount" v-model="discount" class="form-control" v-validate="'required'" placeholder="Discounted price per ticket">
                  <form-error v-if="errors.has('discount')" :errors="errors">
                      {{ errors.first('discount') }}
                  </form-error>
              </div>
              
            <fieldset>
              <legend>
                Ticket Holder(s)
              </legend>
              <table class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, i) in attendees">
                    <td>{{ i + 1}}
                    </td>
                    <td>
                        <div class="form-group" v-bind:class="{ 'has-error' : errors.has('attendees.' + i + '.first_name') || errors['attendees.' + i + '.first_name']}">
                            <input type="text" :name="'attendee[' + i +'][first_name]'" v-model="attendees[i].first_name" class="form-control txo_fn" v-validate="'required'" placeholder="First Name" required>
                            <form-error v-if="errors.has('attendees.' + i + '.first_name') || errors['attendees.' + i + '.first_name']" :errors="errors">
                                {{ errors.first('attendees.' + i + '.first_name') }}
                            </form-error>
                        </div>
                    </td>
                    <td>
                        <div class="form-group" v-bind:class="{ 'has-error' : errors.has('attendees.' + i + '.last_name') || errors['attendees.' + i + '.last_name']}">
                            <input type="text" :name="'attendee[' + i +'][last_name]'"  v-model="attendees[i].last_name" class="form-control txo_ln" v-validate="'required'" placeholder="Last Name" required>
                            <form-error v-if="errors.has('attendees.' + i + '.last_name') || errors['attendees.' + i + '.last_name']" :errors="errors">
                                {{ errors.first('attendees.' + i + '.last_name') }}
                            </form-error>
                        </div>
                    </td>
                    <td><button v-if="i > 0" type="button" class="btn btn-danger btn-xs" @click="remove_attendee(i)"><i class="fa fa-times"></i></button></td>

                </tr>
                <tr>
                    <td></td>
                    <td align="right"><button type="button" class="btn btn-default" @click="add_attendee"><i class="fa fa-plus"></i> Add Ticket</button></td>
                </tr>
                </tbody>
            </table>

            </fieldset>

        </div>

        <div class="modal-footer" v-if="!!order.id_number">
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
        props: [ "m_event_id"],
        components: {VueSimpleSpinner, Countdown},
        data(){
            return {
                tickets: [],
                order: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    expires_at: null,
                    id_number: null
                },
                ticket_id: '',
                event_id: '',
                loading: false,
                user: App.User,
                events: App.Events,
                paid: true,
                alert: {},
                payment: 'full',
                discount: 100,
                attendees: [{first_name: '', last_name: ''}],
                holder: {
                  first_name: '',
                  last_name: ''
                },
                quantity: 1
            }
        },
        mounted() {
            this.tickets  = App.Tickets;
            if (this.m_event_id){
                this.event_id  = this.m_event_id
            }
            this.filter_tickets()
        },
        watch: {
            event_id(value){
                this.filter_tickets()
            }
        },
        methods: {

            submit() {
                this.loading = true;
                let $this  =  this;
                const url = Helpers.auth_url('/api/orders/create', App.User)
                axios.post(url, {
                    event_id: this.event_id,
                    ticket_id: this.ticket_id,
                    id_number: this.order.id_number,
                    phone: this.order.phone,
                    email: this.order.email,
                    first_name: this.order.first_name,
                    lasat_name: this.order.last_name,
                    paid: this.paid ? 'yes' : 'no',
                    payment: this.payment,
                    discount: this.discount,
                    holder: this.holder,
                    attendees: this.attendees
                }).then( (response) => {

                    bus.$emit('order-posted', response.data)
                    this.$notify.success({
                      title: "Success",
                      message: "Message created successfully"
                    })

                    setTimeout(function(){
                        location.reload()
                    }, 2000)
                }).catch( (error) => {
                    if(error.response){
                        let data  = error.response.data;
                        this.alert = { type: 'danger', message: data.message}
                        this.get_server_errors(data)
                        this.loading = false;
                    }
                })
            },
            filter_tickets() {
                let event_id  = this.event_id
                this.tickets  = App.Tickets.filter( (ticket) => {
                    return  ticket.event_id == event_id ;
              })
            },
            get_server_errors(errors){
                for(var key in errors) {
                    if (errors.hasOwnProperty(key)){
                        this.$validator.errorBag.add(key, errors[key][0])
                    }
                }

            },

            add_attendee() {
                this.attendees.push({first_name: '', last_name: ''})
            },

            remove_attendee(i){
                this.attendees.splice(i, 1)
            }

        },
        created(){

            bus.$on('userFound',(msg) => {
                if (msg){
                    Object.assign(this.order, msg)
                    this.holder.first_name  = msg.first_name
                    this.holder.last_name  =  msg.last_name
                    this.valid_user= true;
                }else{
                    this.valid_user  = false;
                }
            })

            bus.$on('user-phone', (value) => {
                this.order.phone  = value;
            })
            bus.$on('user-email', (value) => {
                this.order.email  = value;
            })
            bus.$on('user-first_name', (value) => {
                this.order.first_name  = value;
            })
            bus.$on('user-id_number', (value) => {
                this.order.id_number  = value;
            })
        }
    }
</script>
