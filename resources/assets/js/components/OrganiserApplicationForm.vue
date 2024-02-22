<template>
    <form v-on:submit.prevent="validateBeforeSubmit" method="POST" enctype="multipart/form-data">
        <div class="stepwizard">
            <div class="stepwizard-row">
                <div class="stepwizard-step">
                    <button type="button" class="btn  btn-circle" v-bind:class="{'btn-primary': step === 1 }">1</button>
                    <p>Profile Info</p>
                </div>
                <div class="stepwizard-step">
                    <button type="button" class="btn  btn-circle"  v-bind:class="{'btn-primary': step === 2 }">2</button>
                    <p>Payment</p>
                </div>
                <div class="stepwizard-step">
                    <button type="button" class="btn  btn-circle" disabled="disabled" v-bind:class="{'btn-primary': step === 3 }">3</button>
                    <p>Confirm</p>
                </div>
            </div>
        </div>
        <fieldset v-if="step==1">
            <legend>Organiser Profile Information</legend>
            <div v-if="allow_individual" class="form-group" v-bind:class="{'has-error': errors.has('individual') || errors.individual }">
                <label class="control-label">Trading As: </label>
                <select name="account_type" v-model="organiser.individual" class="form-control" v-validate="'required'">
                    <option v-bind:value="'yes'">Individual</option>
                    <option v-bind:value="'no'">Business</option>
                </select>
                <form-error v-if="errors.account_type" :errors="errors">
                    {{ errors.account_type }}
                </form-error>
            </div>
            <div v-if="organiser.individual === 'yes'" class="media well">
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
            <div v-if="organiser.individual === 'no'">
                <div class="form-group" v-bind:class="{'has-error': errors.has('name') || errors.name }">
                    <label class="control-label">Trading Name</label>
                    <input type="text" name="name" v-model="organiser.name" class="form-control" v-validate="'required|min:3'" placeholder="Trading Name">
                    <form-error v-if="errors.has('name') || errors.name" :errors="errors">
                        {{ errors.first('name') }}
                    </form-error>
                </div>
                <div class="form-group" v-bind:class="{'has-error': errors.has('email') || errors.email }">
                    <label class="control-label">Organiser Email</label>
                    <input type="email" name="email" v-model="organiser.email" v-validate="'required|email'"
                           class="form-control"
                           placeholder="Organiser Email">
                    <form-error v-if="errors.has('email') || errors.email" :errors="errors">
                        {{ errors.first('email') }}
                    </form-error>
                </div>
                <div class="form-group" v-bind:class="{'has-error': errors.has('phone') || errors.phone }">
                    <phone-input-field label="Organiser Phone Number"></phone-input-field>
                    <form-error v-if="errors.has('phone') || errors.phone" :errors="errors">
                        {{ errors.first('phone') }}
                    </form-error>
                </div>
            </div>

        </fieldset>
        <fieldset v-if="step==2">
            <legend>Payment Details</legend>
            <div v-if="organiser.individual == 'no'" class="form-group" v-bind:class="{'has-error': errors.has('account_type') || errors.account_type }">
                <label class="control-label">Account Type</label>
                <select name="account_type" v-model="bank_account.account_type" class="form-control" v-validate="'required|in:bank,paybill'">
                    <option v-for="type in types" v-bind:value="type.id">
                        {{ type.title }}
                    </option>
                </select>
                <form-error v-if="errors.account_type" :errors="errors">
                    {{ errors.account_type }}
                </form-error>
            </div>
            <div v-if="bank_account.account_type == 'bank'" class="form-group" v-bind:class="{'has-error': errors.has('bank_id') || errors.bank_id }">
                <label class="control-label">Bank</label>
                <select name="account_type" v-model="bank_account.bank_id" class="form-control" v-validate="'required'">
                    <option value="">Select Bank</option>
                    <option v-for="option in banks" v-bind:value="option.id">
                        {{ option.name }}
                    </option>
                </select>
                <form-error v-if="errors.bank_id" :errors="errors">
                    {{ errors.bank_id }}
                </form-error>
            </div>
            <div class="form-group" v-bind:class="{'has-error': errors.has('bank_account_name') || errors.bank_account_name }">
                <label class="control-label">{{ bank_account.account_type == 'bank' ? 'Bank Account Name' : 'MPESA Paybill Name'}}</label>
                <input type="text" name="bank_account_name" v-model="bank_account.name" class="form-control" v-validate="'required|min:3'">
                <form-error v-if="errors.has('bank_account_name') || errors.bank_account_name" :errors="errors">
                    {{ errors.first('bank_account_name') }}
                </form-error>
            </div>
            <div class="form-group" v-bind:class="{ 'has-error' : errors.has('bank_account_no') || errors.bank_account_no}">
                <label class="control-label">{{ bank_account.account_type == 'bank' ? 'Bank Account No' : 'MPESA Paybill No'}}</label>
                <input type="text" name="bank_account_no" v-model="bank_account.account_no" class="form-control" v-validate="'required|numeric|min:6|confirmed:bank_account_no_confirmation'">
                <form-error v-if="errors.has('bank_account_no') || errors.bank_account_no" :errors="errors">
                    {{ errors.first('bank_account_no') }}
                </form-error>
            </div>
            <div class="form-group" v-bind:class="{ 'has-error' : errors.has('bank_account_no_confirmation') || errors.bank_account_no_confirmation}">
                <label class="control-label">{{ bank_account.account_type == 'bank' ? 'Confirm Bank Account No' : 'Confirm MPESA Paybill No'}}</label>
                <input type="text" name="bank_account_no_confirmation" v-model="bank_account.account_no_confirmation" class="form-control" v-validate="'required|min:6'">
                <form-error v-if="errors.has('bank_account_no_confirmation') || errors.bank_account_no_confirmation" :errors="errors">
                    {{ errors.first('account_no_confirmation') }}
                </form-error>
            </div>
            <div v-if="bank_account.account_type == 'bank'" class="form-group" v-bind:class="{'has-error': errors.has('bank_account_leaf') || errors.bank_account_leaf }">
                <file-upload-input label="Bank Cheque Book Leaf (Cancelled copy)" name="bank_account_leaf" type="image"></file-upload-input>
                <form-error v-if="errors.has('bank_account_leaf') || errors.bank_account_leaf" :errors="errors">
                    {{ errors.first('bank_account_leaf') }}
                </form-error>
            </div>
        </fieldset>
        <fieldset v-if="step === 3">
            <legend>Confirm Application Details</legend>
            <table class="table table-stripped">
                <thead>
                    <tr>
                        <th colspan="2">Organiser Profile Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Organiser Name:</strong> </td>
                        <td>{{ organiser.name}}</td>
                    </tr>
                    <tr>
                        <td><strong>Organiser Email:</strong> </td>
                        <td>{{ organiser.email}}</td>
                    </tr>
                    <tr>
                        <td><strong>Organiser Phone:</strong> </td>
                        <td>{{ organiser.phone}}</td>
                    </tr>
                </tbody>
            </table>

                <table class="table table-stripped">
                <thead>
                    <tr>
                        <th colspan="2">Payment Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Account Type:</strong> </td>
                        <td>{{ bank_account.account_type}}</td>
                    </tr>
                    <tr v-if="bank_account.account_type  ==  'bank'">
                        <td><strong>Bank:</strong> </td>
                        <td>{{ getBankFromId(bank_account.bank_id).name}}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ bank_account.account_type  ==  'bank' ? 'Bank Account Name' : 'MPESA Paybill Name' }}:</strong> </td>
                        <td>{{ bank_account.name }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ bank_account.account_type  ==  'bank' ? 'Bank Account No' : 'MPESA Paybill No' }}:</strong> </td>
                        <td>{{ bank_account.account_no }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ bank_account.account_type  ==  'bank' ? 'Bank Account No Confirmation' : 'MPESA Paybill No Confirmation' }}:</strong> </td>
                        <td>{{ bank_account.account_no_confirmation }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="well">
                <div class="form-group" v-bind:class="{'has-error' : errors.has('terms') || errors.terms}">
                    <p>
                        <label class="checkbox">
                        <input name="terms" v-validate="'required'" type="checkbox" v-model="terms">
                        I agree to the terms and conditions.
                    </label>
                    <form-error v-if="errors.has('terms') || errors.terms" :errors="errors">
                        {{ errors.first('terms') }}
                    </form-error>
                    </p>
                </div>
            </div>
        </fieldset>
        <fieldset v-if="step == 4">
            <div v-if="submitted" class="text-center">
                <p><i class="fa fa-check-circle-o fa-7x text-success"></i> </p>
                <h4 class="note note-success">
                    Awesome!! Your application has been successfully submitted. We will notify you once it has been approved. Thank you!
                </h4>
            </div>
        </fieldset>

        <div class="modal-footer">
            <vue-simple-spinner v-if="loading" message="Submitting your application..."></vue-simple-spinner>
            <button type="button" v-if="step != 1 || !submitted" class="btn btn-default" v-on:click="back"><i class="fa fa-arrow-left"></i> Back </button>
            <button type="submit" v-if="!submitted"   class="btn btn-primary" :disabled="loading"><i class="fa fa-arrow-right"></i> {{ step == 3 ? 'Finish' : 'Continue'}}</button>
        </div>
    </form>
</template>

<script>

    import Helpers from '../helpers'
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";

    export default {
        props: ['url', "allow_individual"],
        components: {VueSimpleSpinner},
        mounted() {

            this.user = App.User;
            if (this.organiser.individual === 'yes'){
                this.bank_account.type = 'bank'
                Object.assign(this.organiser, {
                    name: this.user.full_name,
                    phone: this.user.phone,
                    email: this.user.email,
                })
            }
        },
        data() {
            return {
                loading: false,
                organiser: {
                    name: '',
                    type: 'business',
                    phone: '',
                    email: '',
                    avatar: '',
                    individual: 'no'
                },
                bank_account: {
                    name: '',
                    account_no: '',
                    account_no_confirmation: '',
                    bank_id: '',
                    account_type: 'bank',
                    owner_id: this.owner_id,
                    owner_type: this.owner_type,
                    bank_account_leaf: ''
                },
                user: {},
                banks: App.Banks,
                types: [
                    {id: 'bank', title: "Bank"},
                    {id: 'paybill', title: "MPESA Paybill"}
                ],
                terms: false,
                step: 1,
                submitted: false
            }
        },
        watch: {
            'organiser.phone'(value) {
                this.$validator.validate('phone',value)
            },
            'organiser.individual'(value){
                this.user  =  App.User;
                if(value === 'yes'){
                    this.bank_account.type = 'bank'
                    Object.assign(this.organiser, {
                        name: this.user.full_name,
                        phone: this.user.phone,
                        email: this.user.email
                    })
                }else {
                    Object.assign(this.organiser, {
                        name: '',
                        phone: '',
                        email: ''
                    })
                }
            }
        },
        methods: {
            submit() {
                this.loading = true;
                var url = Helpers.auth_url(this.url ? this.url : '/api/organisers/create', App.User)

                axios.post(url, {organiser: this.organiser, bank_account: this.bank_account})
                    .then((response) => {
                    
                        this.submitted = true;
                        this.step  = 4; 
                        this.loading =  false;                   
                        setTimeout(() => {
                            window.location.href = '/organisers'
                        }, 2000)
                    })
                    .catch((error) => {
                        let $this = this;
                        this.loading = false;
                        if (error.response) {
                            this.errors = error.response.data;
                        }
                    });
            },
            validateBeforeSubmit(e) {
                if (this.step === 1)
                    this.step1();
                if (this.step === 2)
                    this.step2()
                if (this.step === 3)
                    this.step3()
            },
            step1() {
                this.$validator.attach('name', 'required|min:3');
                this.$validator.attach('email', 'required|email');
                this.$validator.attach('phone', 'required');
                this.$validator.attach('individual', 'required');
                this.$validator.validateAll({
                    name: this.organiser.name,
                    email: this.organiser.email,
                    phone: this.organiser.phone,
                    individual: this.organiser.individual
                }).then(() => {
                    this.step += 1;
                }).catch(() => {
                    // eslint-disable-next-line
                    console.log("Errors: ", this.$validator.errorBag)

                });
            },
            step2() {
                this.$validator.validateAll().then(() => {
                    this.submit();
                }).catch(() => {
                    // eslint-disable-next-line
                    console.log("Errors: ", this.$validator.errorBag)

                });
            },
            step3() {

                this.$validator.attach('bank_account_leaf', 'required');
                this.$validator.validateAll().then(() => {
                    this.step += 1;
                }).catch(() => {
                    // eslint-disable-next-line
                    console.log("Errors: ", this.$validator.errorBag)

                });
            },
            back() {
                if(this.step === 1)
                    return;
                this.step -= 1
            },
            getBankFromId(id) {
                return App.Banks.find(bank => bank.id == id)
            }
        },
        created () {
            var $this = this;
            if(this.organiser.individual === 'yes'){
                this.bank_account.type = 'bank'
                Object.assign(this.organiser, {
                    name: this.user.full_name,
                    phone: this.user.phone,
                    email: this.user.email
                })
            }
            bus.$on('int-phone-number', function (data) {
                $this.organiser.phone = data.full;
            })
            bus.$on('file-avatar-uploaded', function (data) {
                $this.bank_account.bank_account_leaf = data;
            })

            //Listen on the bus for changers to the child components error bag and merge in/remove errors
            bus.$on('errors-changed', (newErrors, oldErrors) => {
                newErrors.forEach(error => {
                    if (!this.errors.has(error.field)) {
                        this.errors.add(error.field, error.msg)
                    }
                })
                if (oldErrors) {
                    oldErrors.forEach(error => {
                        this.errors.remove(error.field)
                    })
                }
            })
        }
    }
</script>