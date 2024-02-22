<template>
    <form v-on:submit.prevent="submit" method="POST" enctype="multipart/form-data">
        <div class="form-group"  v-bind:class="{'has-error': errors.has('name') || errors.name }" >
            <label class="control-label">Organiser Name</label>
            <input type="text" name="name" v-model="organiser.name"  v-validate="'required|alpha|min:3'" class="form-control" placeholder="Organiser Name">
            <form-error v-if="errors.has('name')" :errors="errors">
                {{ errors.first('name') }}
            </form-error>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.email}">
            <label class="control-label">Organiser Email</label>
            <input type="email" name="email" v-model="organiser.email" class="form-control"
                   placeholder="Organiser Email">
            <form-error v-if="errors.email" :errors="errors">
                {{ errors.email }}
            </form-error>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.phone}">
            <label class="control-label">Organiser Phone Number</label>
            <input type="text" name="phone" v-model="organiser.phone" class="form-control" placeholder="Phone Number">
            <form-error v-if="errors.phone" :errors="errors">
                {{ errors.phone }}
            </form-error>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.avatar}">
            <label class="control-label">Organiser Logo</label>
            <input type="file" name="avatar" class="form-control" v-on:change="fileChanged();">
            <form-error v-if="errors.avatar" :errors="errors">
                {{ errors.avatar }}
            </form-error>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-block"> Create Organiser</button>
        </div>
    </form>
</template>

<script>

    import Helpers from '../helpers'

    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data() {
            return {
                loading: false,
                organiser: {
                    name: '',
                    type: 'business',
                    phone: '',
                    email: '',
                    avatar: ''
                },

            }
        },
        methods: {
            submit() {
                var url = Helpers.auth_url('/api/organisers/new', App.User)
                axios.post(url, this.organiser)
                    .then((response) => {
                        window.location.href = '/' + response.data.slug+ '/organiser/dashboard'
                    })
                    .catch((error) => {
                        let $this = this;
                        if (error.response) {
                            this.errors = error.response.data;
                        }
                    });
            },
            fileChanged() {
                var formData = new FormData();
                formData.append('type', 'image');
                formData.append('file', event.target.files[0]);

                var url = Helpers.auth_url('/api/uploads/avatar', App.User)
                axios.post(url, formData)
                    .then((response) => {
                        this.organiser.avatar = response.data.path;
                    })
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
s