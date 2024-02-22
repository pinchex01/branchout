<template>
    <form v-on:submit.prevent="register" method="POST">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group" v-bind:class="{ 'has-error' : errors.first_name}">
                    <label class="control-label">First Name</label>
                    <input type="text" name="first_name" v-model="user.first_name" class="form-control">
                    <form-error v-if="errors.first_name" :errors="errors">
                        {{ errors.first_name }}
                    </form-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group" v-bind:class="{ 'has-error' : errors.last_name}">
                    <label class="control-label">First Name</label>
                    <input type="text" name="last_name" v-model="user.last_name" class="form-control">
                    <form-error v-if="errors.last_name" :errors="errors">
                        {{ errors.last_name }}
                    </form-error>
                </div>
            </div>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.email}">
            <label class="control-label">Email</label>
            <input type="email" name="email" v-model="user.email" class="form-control">
            <form-error v-if="errors.email" :errors="errors">
                {{ errors.email }}
            </form-error>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.phone}">
            <label class="control-label">Phone Number</label>
            <input type="text" name="phone" v-model="user.phone" class="form-control">
            <form-error v-if="errors.phone" :errors="errors">
                {{ errors.phone }}
            </form-error>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-block"> Continue</button>
        </div>
    </form>
</template>

<script>
    export default {
        props: ['order'],
        mounted() {
            console.log('Component mounted.')
        },
        data() {
            return {
                loading: false,
                user: {
                    first_name: '',
                    last_name: '',
                    phone: '',
                    email: '',
                },

                // array to hold form errors
                errors: [],
            }
        },
        methods: {
            register() {
                axios.post('/api/orders/' + this.order + '/add-attendee',this.user)
                    .then()
                    .catch((error) => {
                        let $this = this;
                        if (error.response) {
                            this.errors = error.response.data;
                        }
                    });
            }
        }
    }
</script>
