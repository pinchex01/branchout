<template>
    <form v-on:submit.prevent="search" method="POST">
        <fieldset>
            <legend>{{ title }}</legend>
            <alert-message v-if="alert.hasOwnProperty('message')" :type="alert.type">{{ alert.message }}</alert-message>
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
                    <div class="form-group">
                        <vue-simple-spinner v-if="loading"> Thinking </vue-simple-spinner>
                        <button v-if="!loading" class="btn btn-default"><i v-bind:class="button_icon"></i> {{ button_name ? button_name : 'Search' }} </button>
                    </div>
                </div>
            </div>
            <div v-if="result && show" class="media well">
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
        </fieldset>
    </form>
</template>

<script>
    import Helpers from '../helpers'
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";

    export default {
        props: [ "title", "button_name", "button_icon", "show_user" ],
        components: {VueSimpleSpinner},
        mounted() {
            this.show = this.show_user != 'false'
        },
        data() {
            return {
                loading: false,
                user: {
                    id_number: '',
                    full_name: '',
                    phone: '',
                    email: '',
                    pk: null
                },
                result: false,
                failed: false,
                message: null,
                alert: {},
                user_found: false,
                show: true
            }
        },

        methods: {
            search() {
                this.loading = true
                var url  = '/api/users/get'

                axios.post(url, {
                    username: this.user.id_number
                }).then( (response) => {
                    var user  = response.data;

                    if(user.pk){
                      this.user = response.data;
                      this.result = true;
                      bus.$emit('userFound',this.user)
                      this.$notify.success({
                        title: "Success",
                        message : "User found with username"
                      })
                    }else{
                      bus.$emit('userFound', null)
                      this.$notify.error({
                        title: "Error",
                        message : "No user found matching the username"
                      })
                    }

                    this.loading = false;
                    return;
                }).catch((error) => {
                    this.loading = false;
                    console.log("Error - ", error)
                    if (!error.response){
                        return;
                    }
                    this.result = false;
                    bus.$emit('userFound', null)

                    if (error.response.status  == 422) {
                        this.get_server_errors(error.response.data);
                    }


                });
            },
            get_server_errors(errors){
                for(var key in errors) {
                    if (errors.hasOwnProperty(key)){
                        this.$validator.errorBag.add(key, errors[key][0])
                    }
                }

            }
        }
    }
</script>
