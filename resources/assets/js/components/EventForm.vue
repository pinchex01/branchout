<template>
    <form v-on:submit.prevent="validateAndSubmit" method="POST" enctype="multipart/form-data">
        <div class="form-group" v-bind:class="{ 'has-error' : errors.has('name') || errors.name}">
            <label class="control-label">Event Name</label>
            <input type="text" name="name" v-model="event.name" v-validate="'required|min:3'" class="form-control" placeholder="Event Name">
            <form-error v-if="errors.has('name') || errors.name" :errors="errors">
                {{ errors.first('name') }}
            </form-error>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.has('description') || errors.description}">
            <label class="control-label">Event Description</label>
            <tiny-mce id="description" v-model="event.description"></tiny-mce>
            <form-error v-if="errors.has('description') || errors.description" :errors="errors">
                {{ errors.first('description') }}
            </form-error>
        </div>
        <div  class="form-group" v-bind:class="{ 'has-error' : errors.has('bank_account_id') || errors.bank_account_id}">
            <label class="control-label">Destination Bank Account</label>
            <select name="bank_account_id" v-model="event.bank_account_id" v-validate="'required'" class="form-control">
                <option value="">Select</option>
                <option v-for="option in bank_accounts" v-bind:value="option.id">
                    {{ option.name }}
                </option>
            </select>
            <form-error v-if="errors.has('bank_account_id') || errors.bank_account_id" :errors="errors">
                {{ errors.first('bank_account_id') }}
            </form-error>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group control" v-bind:class="{ 'has-error' : errors.has('start_date') || errors.start_date}">
                    <label class="control-label">Event Start Date:</label>
                    <el-date-picker
                            v-model="event.start_date"
                            type="datetime"
                            format="yyyy-MM-dd HH:mm"
                            placeholder="Pick a day">
                    </el-date-picker>
                    <form-error v-if="errors.has('start_date') || errors.start_date" :errors="errors">
                        {{ errors.first('start_date') }}
                    </form-error>
                </div>

            </div>
            <div class="col-md-6 col-sm-12">
                <p v-if="!show_end_date" class="mt-30"><a  href="#" v-on:click="toggle_show_end_date()"><i class="fa fa-calendar-plus-o"></i> Add End Date </a></p>
                <div v-if="show_end_date" class="form-group" v-bind:class="{ 'has-error' :errors.has('end_date') || errors.end_date}">
                    <label class="control-label" style="width: 100%">Event End Date: <span class="pull-right"><a href="#"  v-on:click="toggle_show_end_date()"><i class="fa fa-calendar-minus-o"></i> Remove </a></span></label>
                    <el-date-picker
                            v-model="event.end_date"
                            type="datetime"
                            placeholder="Pick a day">
                    </el-date-picker>

                    <form-error v-if="errors.has('end_date') || errors.end_date" :errors="errors">
                        {{ errors.first('end_date') }}
                    </form-error>
                </div>
            </div>
        </div>
        <div class="form-group" v-bind:class="{'has-error': errors.has('avatar') || errors.avatar }">
            <file-upload-input label="Event Poster" name="avatar" type="image"></file-upload-input>
            <form-error v-if="errors.has('avatar') || errors.avatar" :errors="errors">
                {{ errors.first('avatar') }}
            </form-error>
        </div>

        <section id="location">
            <div class="form-title"><h3>Location</h3></div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="">Select location (Drag pin to set location):</label>
                        <div id="map-component" style="width: 100%; height: 400px;"></div>
                    </div>
                </div>

                <div class="col-md-4">

                    <div class="form-group" v-bind:class="{ 'has-error' : errors.has('location') || errors.location}">
                        <label class="control-label">Event Venue/Location</label>
                        <input id="us2-address" type="text" name="location" class="form-control"
                               placeholder="Name of venue e.g street or building" v-model="event.location">
                        <form-error v-if="errors.has('location') || errors.location" :errors="errors">
                            {{ errors.first('location') }}
                        </form-error>
                    </div>
                    <div class="form-group" v-bind:class="{ 'has-error' : errors.has('lat') || errors.lat}">
                        <label class="control-label">Latitude</label>
                        <input id="us2-lat" type="text" name="lat" class="form-control"
                               readonly v-model="event.lat">
                        <form-error v-if="errors.has('lat') || errors.lat" :errors="errors">
                            {{ errors.first('lat') }}
                        </form-error>
                    </div>
                    <div class="form-group" v-bind:class="{ 'has-error' : errors.has('lng') || errors.lng}">
                        <label class="control-label">Latitude</label>
                        <input id="us2-lon" type="text" name="lng" class="form-control"
                               readonly v-model="event.lng">
                        <form-error v-if="errors.has('lng') || errors.lat" :errors="errors">
                            {{ errors.first('lng') }}
                        </form-error>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal-footer">
            <vue-simple-spinner v-show="loading"></vue-simple-spinner>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" :disabled="loading" class="btn btn-primary"> Submit </button>
        </div>
    </form>
</template>
<script>

    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";
    import Helpers from '../helpers'
    import moment from 'moment'
    export default {
        props: [
            'action'
        ],
        components: {
            VueSimpleSpinner
        },
        mounted() {
            this.$validator.attach('avatar', 'required');
            this.$validator.attach('location', 'required');
            this.$validator.attach('lat', 'required');
            this.$validator.attach('lng', 'required');
            this.$validator.attach('start_date', "required");
            this.$validator.attach('description', "required");
            this.$validator.attach('end_date', "nullable");

            var $this  =  this;
            $(function () {
                $("#us2-address").change( function (e) {
                    $this.event.location = $(this).val();
                });
                $("#us2-lat").change(function (e) {
                    $this.event.lat = $(this).val();
                });
                $("#us2-lon").change(function (e) {
                    $this.event.lng = $(this).val();
                });
            })
            if(App.Event)
                this.event  = App.Event;
        },
        data() {
            return {
                loading: false,
                event: {
                    name: '',
                    start_date: '',
                    end_date: '',
                    description: '',
                    avatar: '',
                    location: '',
                    lat: '',
                    lng: '',
                    user_id: null,
                    organiser_id: null,
                    commission: 0,
                    bank_account_id: '',

                },
                bank_accounts: App.OrganiserBankAccounts,
                show_end_date: false
            }
        },
        watch: {
            'event.start_date'(value){
                this.$validator.validate('start_date',value)
            },
            'event.end_date'(value){
                if (value)
                    this.$validator.validate('start_date',value)
            },
            'event.avatar'(value){
                this.$validator.validate('avatar',value)
            },
            'event.contract'(value){
                this.$validator.validate('contract',value)
            },
            'event.location'(value){
                this.$validator.validate('location',value)
            },
            'event.lat'(value){
                this.$validator.validate('lat',value)
            },
            'event.lng'(value){
                this.$validator.validate('lng',value)
            }
        },
        methods: {
            submit() {
                this.event.user_id  =  App.User.id;
                this.event.organiser_id = App.Organiser.id;
                var action  = this.action ? this.action : 'new';
                var url = '';
                if (action === 'edit')
                    url = Helpers.auth_url('/api/events/' + this.event.id + '/edit', App.User)
                else
                    url = Helpers.auth_url('/api/events/new', App.User)

                axios.post(url, this.event)
                    .then((response) => {
                        var event = response.data;
                        window.location.href = '/' + App.Organiser.slug + '/organiser/events'
                    })
                    .catch((error) => {
                        let $this = this;
                        if (error.response) {
                            this.errors = error.response.data;
                        }
                    });
            },
            validateAndSubmit() {
                this.$validator.validateAll({
                    name: this.event.name,
                    description: this.event.description,
                    bank_account_id: this.event.bank_account_id,
                    start_date: moment(this.event.start_date).format('YYYY-MM-DD HH:mm'),
                    end_date: this.event.end_date,
                    avatar: this.event.avatar,
                    location: this.event.location,
                    lat: this.event.lat,
                    lng: this.event.lng,
                }).then(() => {
                    this.submit()
                }).catch(() => {
                    // eslint-disable-next-line
                    console.log("Errors: ", this.$validator.errorBag)

                });
            },
            onDateChange() {
                console.log("Date: ", event.target.val())
            },
            toggle_show_end_date(){
                this.show_end_date = !this.show_end_date
            }
        },
        created () {
            var $this = this;
            bus.$on('file-avatar-uploaded', function (data) {
                $this.event.avatar = data;
            })
            bus.$on('file-contract-uploaded', function (data) {
                $this.event.contract = data;
            })
        }
    }
</script>