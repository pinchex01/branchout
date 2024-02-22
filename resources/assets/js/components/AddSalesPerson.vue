<template>
    <form v-on:submit.prevent="register" method="POST">
        <div v-if="submitted">
            <div class="note note-success">
                {{user.full_name}} has been successfully added to your sales team. We have sent them a notification to inform them
            </div>
        </div>
        <div v-if="failed">
            <div class="note note-danger">
                {{ message }}
            </div>
        </div>
       <div v-if="!submitted">
           <user-search-form></user-search-form>
           <div class="modal-footer" v-if="user.id_number">
               <button type="submit" class="btn btn-primary"> Add</button>
           </div>
       </div>

    </form>

</template>

<script>
    import Helpers from '../helpers';
    export default {
        mounted() {

        },
        data() {
            return {
                loading: false,
                user: {},
                userFound: false,
                errors: [],
                submitted: false,
                failed: false,
                message: ''
            }
        },
        methods: {
            register() {
                var url = Helpers.auth_url('/api/events/' + App.Event.id + '/add-sales-person', App.User)
                axios.post(url,this.user)
                    .then( (response) => {
                        this.submitted  = true;
                        this.failed = false;
                        setTimeout(function () {
                            location.reload();
                        },1500);
                    })
                    .catch((error) => {
                        let $this = this;
                        if (error.response) {
                            this.errors = error.response.data;
                        }

                        var result  = error.response.data;
                        if (result.status == 'fail'){
                            this.failed = true;
                            this.message = result.message;
                        }
                    });
            }
        },
        created () {
            var $this  = this;
            bus.$on('userFound', function (msg) {
                if (msg){
                    $this.user = msg;
                    $this.userFound = true;
                }else{
                    $this.user = {};
                    $this.userFound = false;
                }

            })
        }
    }
</script>
