<template>
    <form v-on:submit.prevent="validateAndSubmit" method="POST" enctype="multipart/form-data">
        <div v-if="!loading">
            <div class="form-group" v-bind:class="{ 'has-error' : errors.has('name') || errors.name}">
                <label class="control-label">Ticket Name</label>
                <input type="text" name="name" v-model="ticket.name" v-validate="'required|min:1'" class="form-control" placeholder="Ticket Name">
                <form-error v-if="errors.has('name') || errors.name" :errors="errors">
                    {{ errors.first('name') }}
                </form-error>
            </div>
            <div class="form-group" v-bind:class="{ 'has-error' : errors.has('description') || errors.description}">
                <label class="control-label">Ticket Description</label>
                <textarea id="txt_desc" name="description" v-model="ticket.description" v-validate="'required|min:10'" class="form-control" ></textarea>
                <form-error v-if="errors.has('description') || errors.description" :errors="errors">
                    {{ errors.first('description') }}
                </form-error>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group" v-bind:class="{ 'has-error' : errors.price}">
                        <label class="control-label">Ticket Price</label>
                        <input type="number" name="price" v-model="ticket.price" class="form-control" placeholder="KES">
                        <form-error v-if="errors.price" :errors="errors">
                            {{ errors.price }}
                        </form-error>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group" v-bind:class="{ 'has-error' : errors.quantity_available}">
                        <label class="control-label">Quantity Available</label>
                        <input type="number" name="quantity_available" v-model="ticket.quantity_available" class="form-control" placeholder="Leave blank for unlimited">
                        <form-error v-if="errors.quantity_available" :errors="errors">
                            {{ errors.quantity_available }}
                        </form-error>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group" v-bind:class="{ 'has-error' : errors.has('on_sale_date') || errors.on_sale_date}">
                        <label class="control-label">Start Sale On:</label>
                        <el-date-picker
                                v-model="ticket.on_sale_date"
                                type="datetime"
                                format="yyyy-MM-dd HH:mm"
                                placeholder="Pick a day">
                        </el-date-picker>
                        <form-error v-if="errors.has('on_sale_date') || errors.on_sale_date" :errors="errors">
                            {{ errors.first('on_sale_date') }}
                        </form-error>
                    </div>

                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group" v-bind:class="{ 'has-error' : errors.has('end_sale_date') || errors.end_sale_date}">
                        <label class="control-label">End Sale On:</label>
                        <el-date-picker
                                v-model="ticket.end_sale_date"
                                type="datetime"
                                format="yyyy-MM-dd HH:mm"
                                placeholder="Pick a day">
                        </el-date-picker>
                        <form-error v-if="errors.has('end_sale_date') || errors.end_sale_date" :errors="errors">
                            {{ errors.first('end_sale_date') }}
                        </form-error>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group" v-bind:class="{ 'has-error' : errors.min_per_person}">
                        <label class="control-label">Min. Per Order </label>
                        <input type="number" name="min_per_person" v-model="ticket.min_per_person" class="form-control" >
                        <form-error v-if="errors.min_per_person" :errors="errors">
                            {{ errors.min_per_person }}
                        </form-error>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group" v-bind:class="{ 'has-error' : errors.max_per_person}">
                        <label class="control-label">Max. Per Order</label>
                        <input type="number" name="max_per_person" v-model="ticket.max_per_person" class="form-control">
                        <form-error v-if="errors.max_per_person" :errors="errors">
                            {{ errors.max_per_person }}
                        </form-error>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <vue-simple-spinner v-if="loading"></vue-simple-spinner>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"> Submit </button>
            </div>
        </div>
        <div v-if="loading">
            <vue-simple-spinner size="massive"></vue-simple-spinner>
        </div>
    </form>
</template>
<script>


    import Helpers from '../helpers'
    import moment from 'moment'
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";

    export default {
        components: {VueSimpleSpinner},
        props: ['event','action'],

        mounted() {
            var $this  =  this;
            this.$validator.attach('on_sale_date', "required");
            this.$validator.attach('end_sale_date', "required");
            if (App.Ticket)
                this.ticket  =  App.Ticket;
        },
        data() {
            return {
                loading: false,
                ticket: {
                    name: '',
                    on_sale_date: '',
                    end_sale_date: '',
                    description: '',
                    min_per_person: 1,
                    max_per_person: 30,
                    quantity_available: '',
                    price: 0,
                    event_id: null
                }
            }
        },
        watch: {
            'ticket.on_sale_date'(value){
                this.$validator.validate('on_sale_date',value)
            },
            'ticket.end_sale_date'(value){
                this.$validator.validate('end_sale_date',value)
            }
        },
        methods: {
            submit() {
                this.loading = true;
                this.ticket.event_id = this.event;
                var url =''
                var action = this.action ? this.action : 'new';
                if(action === 'edit')
                    url  = Helpers.auth_url('/api/tickets/' + this.ticket.id + '/edit', App.User)
                else
                    url  = Helpers.auth_url('/api/tickets/new', App.User)
                axios.post(url, this.ticket)
                    .then((response) => {
                        var ticket = response.data;
                        window.location.href = '/' + App.Organiser.slug + '/organiser/events/' + this.event + '/tickets'
                    })
                    .catch((error) => {
                        let $this = this;
                        this.loading = false;
                        if (error.response) {
                            this.errors = error.response.data;
                        }
                    });
            },
            validateAndSubmit() {
                this.$validator.validateAll({
                    name: this.ticket.name,
                    description: this.ticket.description,
                    on_sale_date: this.ticket.on_sale_date,
                    end_sale_date: this.ticket.end_sale_date
                }).then(() => {
                    this.submit()
                }).catch(() => {
                    // eslint-disable-next-line
                    console.log("Errors: ", this.$validator.errorBag)

                });
            },
            setDates() {
                $('.dt_end').data("DateTimePicker").date(moment(ticket.end_sale_date));
                console.log("Dates: ", moment(ticket.end_sale_date))
                $('.dt_start').data("DateTimePicker").date(moment(ticket.on_sale_date));
            }
        }
    }
</script>
s