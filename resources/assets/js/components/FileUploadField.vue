<template>
    <div class="form-group" v-bind:class="{'has-error': errors.has(name) || errors.name }">
        <div>
            <label v-if="label" class="control-label">{{ label }}</label>
            <input v-if="type == 'image'" type="file" :name="name" class="form-control" v-validate="'required|image'" v-on:change="fileChanged();">
            <input v-if="type != 'image'" type="file" :name="name" class="form-control" v-validate="'required'" v-on:change="fileChanged();">
            <form-error v-if="errors.has(name) || errors.name" :errors="errors">
                {{ errors.first(name) }}
            </form-error>
        </div>
        <vue-simple-spinner v-show="loading"></vue-simple-spinner>
    </div>
</template>

<script>
    import Helpers from '../helpers'
    import VeeValidate from 'vee-validate';
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";

    export default {
        components: {VueSimpleSpinner},
        props: ['url', 'label', 'type','name'],
        mounted() {

        },
        data() {
            return {
                path: '',
                loading: false
            }
        },
        watch: {

        },
        methods: {
            fileChanged() {
                this.loading = true;
                var formData = new FormData();
                formData.append('type', 'image');
                formData.append('file', event.target.files[0]);

                var url = Helpers.auth_url(this.url ? this.url : '/api/uploads/avatar' , App.User)
                axios.post(url, formData)
                    .then((response) => {
                        this.path = response.data.path;
                        bus.$emit('file-'+ this.name +'-uploaded', this.path)
                        this.loading = false;
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