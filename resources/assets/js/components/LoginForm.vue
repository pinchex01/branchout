<template>
    <div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.username}">
            <label class="control-label">Username</label>
            <input type="text" name="username" v-model="user.username" class="form-control" placeholder="Phone Number or Email">
            <form-error v-if="errors.username" :errors="errors">
                {{ errors.username }}
            </form-error>
        </div>

        <div class="form-group" v-bind:class="{ 'has-error' : errors.password}">
            <label class="control-label">Password</label>
            <input type="password" name="password" v-model="user.password" class="form-control">
            <form-error v-if="errors.password" :errors="errors">
                {{ errors.password }}
            </form-error>
        </div>
        <input type="hidden" name="_token" v-model="laravel.crsfToken" />
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data() {
            return {
                loading: false,
                user: {
                    username: '',
                    password: '',
                },
                laravel: {
                    crsfToken: window.Laravel.csrfToken
                },

                // array to hold form errors
                errors: [],
            }
        },
        methods: {
            signin() {
                axios.post('/api/oauth/signin',this.user)
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
