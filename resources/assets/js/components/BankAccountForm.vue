<template>
    <form v-on:submit.prevent="submit" method="POST" enctype="multipart/form-data">
        <div class="form-group" v-bind:class="{ 'has-error' : errors.account_type}">
            <label class="control-label">Account Type</label>
            <select name="account_type" v-model="bank.account_type" class="form-control">
                <option v-for="type in types" v-bind:value="type.id">
                {{ type.title }}
                </option>
            </select>
            <form-error v-if="errors.account_type" :errors="errors">
                {{ errors.account_type }}
            </form-error>
        </div>
        <div v-if="bank.account_type == 'bank'" class="form-group" v-bind:class="{ 'has-error' : errors.bank_id}">
            <label class="control-label">Bank</label>
            <select name="account_type" v-model="bank.bank_id" class="form-control">
                <option value="">Select Bank</option>
                <option v-for="option in banks" v-bind:value="option.id">
                    {{ option.name }}
                </option>
            </select>
            <form-error v-if="errors.bank_id" :errors="errors">
                {{ errors.bank_id }}
            </form-error>
        </div>
        <div  class="form-group" v-bind:class="{ 'has-error' : errors.name}">
            <label class="control-label">{{ bank.account_type == 'bank' ? 'Bank Account Name' : 'MPESA Paybill Name'}}</label>
            <input type="text" name="name" v-model="bank.name" class="form-control">
            <form-error v-if="errors.name" :errors="errors">
                {{ errors.name }}
            </form-error>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.account_no}">
            <label class="control-label">{{ bank.account_type == 'bank' ? 'Bank Account No' : 'MPESA Paybill No'}}</label>
            <input type="text" name="account_no" v-model="bank.account_no" class="form-control">
            <form-error v-if="errors.account_no" :errors="errors">
                {{ errors.account_no }}
            </form-error>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.account_no_confirmation}">
            <label class="control-label">{{ bank.account_type == 'bank' ? 'Confirm Bank Account No' : 'Confirm MPESA Paybill No'}}</label>
            <input type="text" name="account_no" v-model="bank.account_no_confirmation" class="form-control">
            <form-error v-if="errors.account_no_confirmation" :errors="errors">
                {{ errors.account_no_confirmation }}
            </form-error>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-block"> Submit </button>
        </div>
    </form>
</template>

<script>

    import Helpers from '../helpers'

    export default {
        props: ['owner_id', 'owner_type'],
        mounted() {
            console.log('Component mounted.')
        },
        data() {
            return {
                loading: false,
                bank: {
                    name: '',
                    account_no: '',
                    account_no_confirmation: '',
                    bank_id: '',
                    account_type: 'bank',
                    owner_id: this.owner_id,
                    owner_type: this.owner_type
                },

                banks: this.getBanks(),
                types: [
                    {id: 'bank', title: "Bank"},
                    {id: 'paybill', title: "MPESA Paybill"}
                ],
                // array to hold form errors
                errors: [],
            }
        },
        methods: {
            submit() {
                var url = Helpers.auth_url('/api/bank-accounts/new', App.User)
                axios.post(url, this.bank)
                    .then((response) => {
                        location.reload()
                    })
                    .catch((error) => {
                        let $this = this;
                        if (error.response) {
                            this.errors = error.response.data;
                        }
                    });
            },
            getBanks() {
                return App.Banks
            }
        }
    }
</script>
s