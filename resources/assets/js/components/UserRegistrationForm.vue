<template>
    <div class="panel panel-default">

        <div class="panel-body">
            <form  v-loading.body="loading" v-on:submit.prevent="validate" method="POST" v-if="!registered" class="form-signup">
                <h3 class="mt-30">Sign up</h3>
                <p>to continue to PartyPeople</p>
                <alert-message v-if="failed" type="alert-danger">{{ message }}</alert-message>
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
                <div class="form-group" v-bind:class="{ 'has-error' : errors.has('gender') || errors.gender }">
                    <label class="control-label">Gender</label>
                    <select name="gender" v-model="user.gender" class="form-control" v-validate="'required'">
                      <option value="">Select Gender</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                    <form-error v-if="errors.has('gender') || errors.gender " :errors="errors">
                        {{ errors.first('gender') }}
                    </form-error>
                </div>
                <div class="form-group" v-bind:class="{ 'has-error' : errors.has('username') || errors.email }">
                    <label class="control-label">Username</label>
                    <input type="text" name="username" v-model="user.username" class="form-control" v-validate="'required|alpha_dash|min:3|max:15'">
                    <form-error v-if="errors.has('username') || errors.username " :errors="errors">
                        {{ errors.first('username') }}
                    </form-error>
                </div>
                <div class="form-group" v-bind:class="{ 'has-error' : errors.has('email') || errors.email }">
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
                <div class="form-group" v-bind:class="{ 'has-error' : errors.has('password') || errors.password}">
                    <label class="control-label">Password</label>
                    <input type="password" name="password" v-model="user.password" class="form-control" v-validate="'required|confirmed'">
                    <form-error v-if="errors.has('password') || errors.phone" :errors="errors">
                        {{ errors.first('password') }}
                    </form-error>
                </div>
                <div class="form-group"  v-bind:class="{ 'has-error' : errors.has('password_confirmation') || errors.password_confirmation}">
                    <label class="control-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" v-model="user.password_confirmation" class="form-control" v-validate="'required'">
                    <form-error v-if="errors.has('password_confirmation') || errors.password_confirmation" :errors="errors">
                        {{ errors.first('password_confirmation') }}
                    </form-error>
                </div>
                <div class="form-actions" v-if="!registered && !loading">
                    <button type="submit" class="btn btn-danger btn-block"> Continue</button>
                </div>
            </form>
            <div v-if="registered" class="text-center">
                <p><i class="fa fa-check-circle-o fa-7x text-success"></i> </p>
                <h4 class="note note-success">
                    Your account has been created successfully! <a href="/auth/signin">Sign in</a> to continue
                </h4>
            </div>
        </div>
    </div>
</template>

<script>
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";
    export default {
        components: {VueSimpleSpinner},
        mounted() {
            this.$validator.attach('phone', 'required');
        },
        data() {
            return {
                loading: false,
                user: {
                    first_name: '',
                    last_name: '',
                    phone: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    gender: '',
                    username: ''
                },
                step: 'username',
                failed: false,
                message: '',
                registered: false
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
                axios.post('/api/oauth/users/register',{
                        first_name: this.user.first_name,
                        last_name: this.user.last_name,
                        email: this.user.email,
                        phone: this.user.phone.replace('+',''),
                        username: this.user.username,
                        password: this.user.password,
                        password_confirmation: this.user.password_confirmation,
                        gender: this.user.gender
                    })
                    .then( (response) => {
                        this.registered  = true;
                        this.loading = false
                    })
                    .catch((error) => {
                        let $this = this;
                        if (!error.response) return;

                        if (error.response.status  == 422) {
                            let errors  = error.response.data;
                            this.get_server_errors(errors)
                            //this.errors = error.response.data
                        }
                        this.loading  = false;
                    });
            },
            validate(){
                this.$validator.validateAll({
                    first_name: this.user.first_name,
                    last_name: this.user.last_name,
                    email: this.user.email,
                    phone: this.user.phone,
                    username: this.user.username,
                    password: this.user.password,
                    password_confirmation: this.user.password_confirmation,
                    gender: this.user.gender
                }).then(() => {
                    this.register();
                }).catch(( error) => {
                    // eslint-disable-next-line
                    this.$notify.error({
                        title: 'Error',
                        message: 'There seems to be an error'
                    })

                });
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
