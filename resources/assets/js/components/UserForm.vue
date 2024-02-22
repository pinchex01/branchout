<template>
    <form v-loading.body="loading" v-on:submit.prevent="validate" method="POST" class="form-signup">
        <alert-message v-if="alert.hasOwnProperty('message')" :type="alert.type">{{ alert.message }}</alert-message>
        <div class="row">
            <div class="col-sm-6 col">
                <div class="form-group" v-bind:class="{ 'has-error' :  errors.has('first_name') || errors.first_name }">
                    <label class="control-label">First Name</label>
                    <input type="text" name="first_name" v-model="user.first_name" class="form-control" v-validate="'required'">
                    <form-error v-if=" errors.has('first_name') || errors.first_name " :errors="errors">
                        {{ errors.first('first_name') }}
                    </form-error>
                </div>
            </div>
            <div class="col-sm-6 col">
                <div class="form-group" v-bind:class="{ 'has-error' :  errors.has('last_name') || errors.last_name }" style="padding-right: 0 !important;">
                    <label class="control-label">Last Name</label>
                    <input type="text" name="last_name" v-model="user.last_name" class="form-control" v-validate="'required'">
                    <form-error v-if=" errors.has('last_name') || errors.last_name " :errors="errors">
                        {{ errors.first('last_name') }}
                    </form-error>
                </div>
            </div>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.has('username') }">
            <label class="control-label">Username</label>
            <input type="text" name="username" v-model="user.username" class="form-control" v-validate="'required'" placeholder="e.g ID No, Passport">
            <form-error v-if="errors.has('username') " :errors="errors">
                {{ errors.first('username') }}
            </form-error>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.has('gender') || errors.gender }">
            <label class="control-label">Gender</label>
            <select name="gender" v-model="user.gender" class="form-control" v-validate="'required'">
                <option :value="''"></option>
                <option :value="'Male'">Male</option>
                <option :value="'Female'">Female</option>
            </select>
            <form-error v-if="errors.has('gender') || errors.gender " :errors="errors">
                {{ errors.first('gender') }}
            </form-error>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.has('email')}">
            <label class="control-label">Email Address</label>
            <input type="text" name="email" v-model="user.email" class="form-control" v-validate="'required'">
            <form-error v-if="errors.has('email') || errors.email " :errors="errors">
                {{ errors.first('email') }}
            </form-error>
        </div>
        <div class="form-group" v-bind:class="{'has-error': errors.has('phone') || errors.phone }">
            <phone-input-field label="Phone Number"></phone-input-field>
            <form-error v-if="errors.has('phone') || errors.phone" :errors="errors">
                {{ errors.first('phone') }}
            </form-error>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" :disabled="loading"><i class="fa fa-save"></i> Save </button>
        </div>
    </form>
</template>

<script>
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";
    import Helpers from '../helpers'

    export default {
        props: [ "user_pk" ],
        components: {VueSimpleSpinner},
        mounted() {
            this.$validator.attach('phone', 'required');
            if (this.user_pk)
                this.fetchUser();
        },
        data() {
            return {
                loading: false,
                user: {
                    first_name: '',
                    last_name: '',
                    phone: '',
                    email: '',
                    gender: '',
                    username: '',
                },
                alert: {}
            }
        },
        watch: {
            'user.phone'(value) {
                this.$validator.validate('phone',value)
            }
        },
        methods: {
            register() {
                this.loading = true;
                let url  = Helpers.auth_url('/api/users/new', App.User);
                axios.post(url,this.user)
                    .then( (response) => {
                        this.$notify.success({
                          title: "Success",
                          message: "Changes saved successfully"
                        })
                        location.reload();
                    })
                    .catch((error) => {
                        let $this = this;
                        if (!error.response) return;

                        if (error.response.status  == 422) {
                            let errors  = error.response.data;
                            this.get_server_errors(errors)
                            //this.errors = error.response.data
                        }else {
                          this.alert = { type: 'danger', message: error.response.data.message}
                        }
                        this.loading  = false;
                        this.$notify.error({
                          title: "Errors",
                          message: "Seems like something went wrong"
                        })
                    });
            },
            validate(){
                this.$validator.validateAll().then(() => {
                    this.register();
                }).catch(() => {
                    // eslint-disable-next-line
                    console.log("Errors: ", this.$validator.errorBag)

                });
            },
            fetchUser() {
                this.loading = true;
                let url  = '/api/search/users?pk='+this.user_pk

                axios.get(url)
                    .then( (response) => {
                        Object.assign(this.user, response.data)
                        this.loading = false;
                    })
                    .catch((error) => {
                        this.$notify.error({
                            title: "Error",
                            message: "User not found"
                        })
                        this.loading = false;
                    })
            },
            get_server_errors(errors){
                for(var key in errors) {
                    if (errors.hasOwnProperty(key)){
                        this.$validator.errorBag.add(key, errors[key][0])
                    }
                }

            }
        },
        created () {
            let $this = this;
            bus.$on('int-phone-number', function (data) {
                $this.user.phone = data.full;
            })
        }
    }
</script>
