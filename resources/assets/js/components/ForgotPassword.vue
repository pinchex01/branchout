<template>
    <form  method="POST" v-on:submit.prevent="submit"  v-loading.body="loading">
        <div class="mt-20">
          <alert-message v-if="alert.hasOwnProperty('message')" :type="alert.type">{{ alert.message }}</alert-message>
        </div>
        <div v-if="step == 'username'">
            <h3 class="mt-30">Forgot Password</h3>
            <p>Find your account</p>

            <div class="form-group mt-30">
                <input type="text" name="username" placeholder="Username, Email or Phone" class="form-control" v-model="auth.username" v-validate="'required'">
            </div>
        </div>
        <div v-if="step == 'mode'">
            <h3 class="mt-30">Hi</h3>

            <div v-if="!reset">
              <p>How would you like to reset your password?</p>
              <div class="form-group mt-30">
                  <el-radio class="radio" v-model="auth.mode" label="phone"> Send SMS to {{ masked_phone }}</el-radio>
              </div>
              <div class="form-group">
                  <el-radio class="radio" v-model="auth.mode" label="email"> Send email to {{ masked_email }}</el-radio>
              </div>
            </div>

            <div v-if="auth.mode  == 'phone' && reset">
              <p>Enter the 5-digit password reset code that has been sent to your phone</p>
              <div class="form-group mt-30">
                  <input type="text" name="code" placeholder="Reset code XXXXX" class="form-control" v-model="auth.code" v-validate="'required'">
              </div>
            </div>

            <div v-if="auth.mode  == 'email' && reset">
              <p>Passwod reset link sent!</p>
              <div class="note note-success">
                  <i class="fa fa-check-circle text-success"></i> Password reset link has been sent to your email {{ masked_email}}. If you have not received the email, click resend
              </div>
            </div>

        </div>

        <div v-if="step == 'change'">
            <h3 class="mt-30">Almost there</h3>
            <p>What is your new password</p>

            <div class="form-group mt-30">
                <input type="password" name="password" placeholder="New Password" class="form-control" v-model="auth.password" v-validate="'required'">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Retype your new password" class="form-control" v-model="auth.password_confirmation" v-validate="'required'">
            </div>
        </div>

        <div class="form-actions mt-20">
            <button v-if="reset" type="button" class="btn btn-default" :disabled="loading" v-on:click="forgot_request"><i class="fa fa-refresh"></i> Resend </button>
            <button v-if="next" type="submit" class="btn btn-danger pull-right" :disabled="loading"> Next <i class="fa fa-arrow-right"></i></button>
            <button v-if="!next && reset" type="button" class="btn btn-danger pull-right" :disabled="loading" v-on:click="submit_code"> Verify </button>
        </div>
    </form>
</template>
<script>
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";
    export default {
        props: [ "next_step", "user_pk", "_change", "reset_pk" ],
        components: {VueSimpleSpinner},
        mounted() {
            this.$validator.attach('phone', 'required');
            if(this.user_pk){
              this.auth.pk  = this.user_pk
              this.step = 'change'
            }

            if(this.reset_pk){
              this.reset_ref = this.reset_pk
            }
        },
        data() {
            return {
                loading: false,
                auth: {
                    pk: '',
                    username: '',
                    password: '',
                    password_confirmation: '',
                    old_password: '',
                    mode: 'phone',
                    code: ''
                },
                laravel: {
                    crsfToken: window.Laravel.csrfToken
                },
                step: 'username',
                alert: {},
                masked_phone: '',
                masked_email: '',
                reset: false,
                reset_ref: null,
                next: true
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
                axios.post('/api/oauth/forgot',{ username: this.auth.username})
                    .then( (response) => {
                        let data  = response.data;
                        this.auth.pk = data.pk
                        this.masked_email = data.masked_email
                        this.masked_phone  = data.masked_phone
                        this.step = 'mode'
                        this.loading = false;

                    })
                    .catch((error) => {
                      this.$notify.error({
                          title: "Error",
                          message: "Invalid username"
                      });
                      this.loading = false;

                      this.alert = {type: 'danger', message: 'Invalid username'}

                    });
            },
            forgot_request() {
              this.loading = true;
                this.alert = {}

                axios.post('/api/oauth/forgot/request',{ user_ref: this.auth.pk, mode: this.auth.mode , _token: this.laravel.crsfToken})
                    .then( (response) => {
                      let data  = response.data;
                      this.reset_ref = data.reset_pk;
                      this.reset = true;

                      if (this.auth.mode == 'phone'){

                      }

                      this.next = false;
                      this.loading = false;
                    })
                    .catch((error) => {
                      this.$notify.error({
                          title: "Error",
                          message: "Something doesn't seem right"
                      });

                      this.loading = false;
                    });
            },

            submit_code() {
              this.loading = true;
                this.alert = {}

                axios.post('/api/oauth/forgot/code',{ ref: this.reset_ref, code: this.auth.code  , _token: this.laravel.crsfToken})
                    .then( (response) => {
                      let data  = response.data;
                      this.step = 'change';
                      this.reset = false;
                      this.next = true
                      this.loading = false;
                    })
                    .catch((error) => {
                      this.$notify.error({
                          title: "Error",
                          message: "Invalid reset code"
                      });

                      this.loading = false;

                      this.alert = {type: 'danger', message: 'Invalid reset code'}
                    });
            },

            change_password() {
              this.loading = true;
                this.alert = {}

                axios.post('/api/oauth/change',{
                    type: this._change == "change" ? 'change' : 'forgot',
                    user_ref: this.auth.pk,
                    reset_ref: this.reset_ref ,
                    password: this.auth.password,
                    password_confirmation: this.auth.password_confirmation,
                    _token: this.laravel.crsfToken
                  })
                    .then( (response) => {
                      let data  = response.data;
                      this.$notify.success({
                          title: "Awesome",
                          message: "Your password has been saved successfully. Sign in with your new password"
                      });

                      setTimeout( () => {
                        window.location.href = "/auth/signin"
                      }, 2000)

                    })
                    .catch((error) => {
                      this.$notify.error({
                          title: "Error",
                          message: "Something doesn't seem right"
                      });

                      this.loading = false;

                      this.alert = {type: 'danger', message: "Your passwords don't seem to match"}
                    });
            },

            submit() {
              this.loading = true;
                switch (this.step){
                    case 'username':
                        this.lookup();
                        break;
                    case 'mode' && this.reset:
                        this.submit_code();
                        break;
                    case 'mode':
                        this.forgot_request();
                        break;

                    case 'change':
                        this.change_password();
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

            },
            next() {
              this.loading  = true;
              setTimeout(() => {
                this.loading  = false;
              }, 2000)
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
