<template>
    <form v-on:submit.prevent="search" method="POST">
        <fieldset>
            <legend>{{ title }}</legend>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group" v-bind:class="{ 'has-error' : errors.has('id_number')}">
                        <input type="text" name="id_number" v-model="user.id_number" class="form-control" placeholder="Username" v-validate="'required'">
                        <form-error v-if="errors.has('id_number')" :errors="errors">
                            {{ errors.first('id_number') }}
                        </form-error>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group" v-bind:class="{ 'has-error' : errors.has('first_name')}">
                        <input type="text" name="first_name" v-model="user.first_name" class="form-control" placeholder="First Name as per ID">
                        <form-error v-if="errors.has('first_name')" :errors="errors">
                            {{ errors.first('first_name') }}
                        </form-error>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <vue-simple-spinner v-if="loading"> Thinking </vue-simple-spinner>
                        <button v-if="!loading" class="btn btn-default"><i v-bind:class="button_icon"></i> {{ button_name ? button_name : 'Search' }} </button>
                    </div>
                </div>
            </div>
            <div id="message" v-if="message">
                <div class="note note-danger">{{ message }}</div>
            </div>
            <div v-if="user.registered && result" class="media well">
                <div class="media-left">
                    <a href="#">
                        <img class="media-object" src="/img/generic-avatar.png" alt="" height="auto" width="50">
                    </a>
                </div>
                <div class="media-body">
                    <h4 class="media-heading">{{user.full_name}}</h4>
                    <strong>ID No.</strong>  {{ user.id_number }}<br>
                    <strong>Email</strong>  {{ user.email}}<br>
                    <strong>Phone No.</strong>  {{ user.phone}}<br>
                </div>
            </div>
            <div class="form-group" v-bind:class="{ 'has-error' : errors.email}" v-if="!user.registered && result">
                <label class="control-label">Email</label>
                <input type="email" name="email" v-model="user.email" class="form-control" required v-on:change="change('email', $event.target.value)">
                <form-error v-if="errors.email" :errors="errors">
                    {{ errors.email }}
                </form-error>
            </div>
            <div class="form-group" v-bind:class="{'has-error': errors.has('phone') || errors.phone }" v-if="!user.registered && result">
                <phone-input-field label="Phone Number" v-model="user.phone"></phone-input-field>
                <form-error v-if="errors.has('phone') || errors.phone" :errors="errors">
                    {{ errors.first('phone') }}
                </form-error>
            </div>
        </fieldset>
    </form>
</template>

<script>
    import Helpers from '../helpers'
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";

    export default {
        props: [ "title", "button_name", "button_icon" ],
        components: {VueSimpleSpinner},
        mounted() {

        },
        data() {
            return {
                loading: false,
                user: {
                    id_number: '',
                    first_name: '',
                    phone: '',
                    email: '',
                    registered: 0,
                    id: null
                },
                result: false,
                failed: false,
                message: null,
            }
        },
        watch: {
            'user.phone'(value) {
                bus.$emit('user-phone', value)
            },
            'user.email'(value) {
                bus.$emit('user-email', value)
            }
        },
        methods: {
            search() {
                this.loading = true
                var url  = '/api/users/fetch'
                axios.post(url, {
                    id_number: this.user.id_number,
                    first_name: this.user.first_name
                }).then( (response) => {
                    this.user = response.data;
                    this.result = true;
                    bus.$emit('userFound',this.user)
                    this.loading = false;
                }).catch((error) => {
                    let $this = this;
                    if (!error.response){
                        return;
                    }
                    this.result = false;
                    bus.$emit('userFound', null)
                    this.loading = false;
                    if(error.response.status  == 430){
                        this.message = error.response.data.message
                    }
                    if (error.response.status  == 422) {
                        this.$validator.errorBag.add('id_number', error.response.data.id_number[0])
                        //this.errors = error.response.data
                    }


                });
            },
            change(key, value) {
                alert(value)
                var obj = {};
                obj[key] = value
                Object.assign(this.user,obj )
                bus.$emit('user-'+key, value)
            }
        },
        created () {
            let $this = this;
            bus.$on('int-phone-number', function (data) {
                $this.user.phone = data.full;
                bus.$emit('user-phone', data.full)
            })
        }
    }
</script>
