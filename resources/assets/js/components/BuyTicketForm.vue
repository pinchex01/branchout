<template>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" v-model="crsfToken"/>
        <div class="table-responsive">
            <table class="table table-condensed table-hover">
            <thead>
            <tr>
                <th>Ticket</th>
                <th class="hidden-xs">Unit Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(ticket, index) in all_tickets">
                <td>{{ ticket.name }}
                    <span class="visible-xs">@ {{ ticket.price }} </span>
                </td>
                <td class="hidden-xs">{{ ticket.price }}</td>
                <td>
                    <div v-if="is_ticket_on_sale(ticket)" class="form-group" v-bind:class="{ 'has-error' : errors.has('tickets.'+ ticket.id) || errors['tickets.'+ ticket.id]}">
                        <select :name="'tickets['+ ticket.id +']'" v-validate="'min:'+ticket.min_per_person +'|max:' + (ticket.quantity_available - ticket.quantity_sold)"
                                v-on:change="add_remove_ticket(ticket.id, $event.target.value)">
                            <option selected value="0">0</option>
                            <option v-for="n  in ticket.max_per_person  < 10 ? ticket.max_per_person : 10" :value="n">{{ n }}</option>
                        </select>
                        <form-error v-if="{ 'has-error' : errors.has('tickets.'+ ticket.id) || errors['tickets.'+ ticket.id]}" :errors="errors">
                            {{ errors.first('tickets.'+ ticket.id) }}
                        </form-error>
                    </div>
                    <span class="text-center" v-if="!is_ticket_on_sale(ticket)">
                        <span class="text-danger" v-if="ticket_sale_status(ticket) == 'history'"> Sale ended on {{ ticket.end_sale_date}}</span>
                        <span class="text-info" v-if="ticket_sale_status(ticket) == 'future'"> Sale begins on {{ ticket.on_sale_date}}</span>
                    </span>
                </td>
                <td>{{ costs[ticket.id] || 0 }}</td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" align="right">
                    Sub-Total
                </td>
                <td>{{ sub_total }}</td>
            </tr>
            <tr>
                <td>
                    <div v-if="!agent_code" class="form-group" v-bind:class="{ 'has-error' : errors.has('sales_agent_code') || errors.sales_agent_code}">
                        <input class="form-control" type="text" name="sales_agent_code" v-model="sales_agent_code" placeholder="Agent Number">
                        <form-error v-if=" errors.has('sales_agent_code') || errors.sales_agent_code" :errors="errors">
                            {{ errors.first('sales_agent_code') || errors.sales_agent_code }}
                        </form-error>
                    </div>
                </td>
                <td colspan="2" align="right">
                    Discount
                </td>
                <td>{{ discount }}</td>
            </tr>
            <tr>
                <td colspan="2">

                </td>
                <td colspan="1" align="right">
                    <strong>TOTAL</strong>
                </td>
                <td>{{ total }}</td>
            </tr>
            </tfoot>
        </table>
        </div>
        <div class="modal-footer">
            <vue-simple-spinner v-if="loading"> Thinking </vue-simple-spinner>
            <button type="submit" class="btn btn-danger btn-large" :disabled="loading || tickets.length < 1"> PROCEED</button>
        </div>
    </form>
</template>
<script>

    import Helpers from '../helpers'
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";
    import Countdown from 'vuejs-countdown'
    import moment from 'moment'
    export default {
        props: [ "event", "cart_id", "agent_code" , "points"],
        components: {VueSimpleSpinner, Countdown},
        data(){
            return {
                step: 1,
                all_tickets: App.Tickets,
                tickets: [],
                costs: {},
                sub_total: 0,
                coupon: 0,
                discount: 0,
                total: 0,
                sales_agent_code: '',

                loading: false,
                crsfToken: window.Laravel.csrfToken
            }
        },
        watch: {
            tickets(value){
                let total = 0;
                for (var key in this.costs) {
                    if (this.costs.hasOwnProperty(key)) {
                        total  += this.costs[key];
                    }
                }
                this.sub_total = total
            },
            sub_total(value){
                this.calculate_total()
            },
            discount(value){
                this.calculate_total();
            }
        },
        methods: {
            calculate_total() {
                this.total  = (this.sub_total - this.discount)
            },

            add_remove_ticket(ticket_id, quantity){
                let ticket  = this.get_ticket(ticket_id);

                if(quantity > 0){
                    let selected  = { "ticket_id": ticket.id, "quantity": quantity, 'total': ticket.price * quantity }
                    this.costs[ticket.id] = ticket.price * quantity
                    this.remove_ticket(ticket.id);
                    this.add_ticket(selected);
                }else{
                    //remove item from array
                    this.remove_ticket(ticket.id);
                }

            },
            add_ticket(ticket = {}){
                this.tickets.push(ticket)
            },
            remove_ticket(ticket_id){
                this.tickets = this.tickets.filter( (item) => {
                    return item.ticket_id !== ticket_id
                })
            },
            get_ticket(ticket_id) {
                const tickets  = this.all_tickets;
                this.costs[ticket_id] = 0
                return tickets.find( (item) => {
                    return  item.id === ticket_id;
                })

            },
            ticket_sale_status(ticket){
                var status  = 'active';
                if(!this.is_ticket_on_sale(ticket)){
                  if (moment(ticket.end_sale_date).isBefore(moment(), 'minute'))
                      status  = 'history'
                  else
                      status  = 'future'
                }

                return status
            },
            /**
             * Check if ticket is on sale
             *
             * @param ticket
             * @returns {boolean}
             */
            is_ticket_on_sale(ticket) {
                return moment(ticket.on_sale_date, 'YYYY-MM-DD HH:mm').isBefore(moment().add(2,'minutes'), 'minute') && moment(ticket.end_sale_date, 'YYYY-MM-DD HH:mm' ).add(2, "minutes").isAfter(moment(), 'minute')
            }

        },
        created(){

        }
    }
</script>
