<template>
    <form  method="POST" v-on:submit.prevent="authSubmit"  v-loading.body="loading">

        <div>
            <h3 class="mt-30">Sign in</h3>
            <p>to continue to PartyPeople</p>
            <alert-message v-if="alert.hasOwnProperty('message')" :type="alert.type">{{ alert.message }}</alert-message>
            <div class="form-group mt-30">
                <input type="text" name="username" placeholder="Username, Email or Phone" class="form-control" v-model="auth.username" v-validate="'required'">
            </div>
            <div class="form-group mt-30">
                <input type="password" name="password" placeholder="Enter your password" class="form-control" v-model="auth.password" v-validate="'required'">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-danger btn-block" :disabled="loading"> Sign In</button>
        </div>

        <div align="center" class="pt-10">
          <!-- <a href="/auth/fbredirect" class="pull-left"><img src="/images/fblogin.png" style="width: 200px;"></a> -->
          <!-- <a href="/auth/gmredirect" class="pull-left"><img src="/images/gmlogin.png" style="width: 200px;"></a> -->
        </div>
    </form>
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
                auth: {
                    username: '',
                    password: '',
                },
                laravel: {
                    crsfToken: window.Laravel.csrfToken
                },
                step: 'username',
                failed: false,
                message: '',
                registered: false,
                alert: {}
            }
        },
        watch: {
            step(value){
                switch (value){
                    case 'username':
                        this.registered = false;
                        this.failed =  false;
                        break;
                    case 'register':
                        this.registered = false;
                        this.failed =  false;
                        break;
                }
            }
        },
        methods: {
            lookup() {
                this.alert = {}
                this.loading = true;
                axios.post('/api/oauth/lookup',{ username: this.auth.username})
                    .then( (response) => {
                        let user  = response.data.user;

                        this.user = user;
                        if(user.status == 'pending'){
                           window.location.href = '/auth/otp?ref='+user.pk;
                           return;
                        }
                        if(user.registered){

                            this.step = 'password'
                            this.loading = false;
                        }else{
                            this.$notify.error({
                                title: "Not Found",
                                message: "Invalid username"
                            });
                            this.loading = false;

                            this.alert = {type: 'danger', message: 'Invalid username'}

                        }

                    })
                    .catch((error) => {
                        let $this = this;
                        this.loading = false;
                        if (error.response) {
                            this.errors = error.response.data;
                        }
                    });
            },
            authSubmit() {
                this.alert = {}
                this.loading = true;

                axios.post('/api/oauth/signin',{ username: this.auth.username, password: this.auth.password, _token: this.laravel.crsfToken})
                    .then( (response) => {
                        this.failed = false;

                        let user  = response.data.user;
                        let auth_token  = response.data.authorize_token;
                        window.location.href = '/auth/authorize?username='+ this.auth.username +'&token=' + auth_token + '&go_to=' +response.data.go_to
                    })
                    .catch((error) => {
                        let $this = this;
                        this.failed = true;
                        this.loading = false;
                        if (error.response) {
                            this.errors = error.response.data;
                            this.alert = { type: 'danger', message: error.response.data.message}
                        }
                    });
            },

            submit() {
                switch (this.step){
                    case 'username':
                        this.lookup();
                        break;
                    case 'password':
                        this.authSubmit();
                        break;
                    default:
                        break;
                }
            },
            back() {
                location.reload()
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
                $this.auth.username = data.full;
            })
        }
    }
</script>
