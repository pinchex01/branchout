<template>
    <form method="post" v-on:submit.prevent="submit()" v-if="user">
        <alert-message v-if="alert.hasOwnProperty('message')" :type="alert.type">{{ alert.message }}</alert-message>
        <div class="form-group" v-bind:class="{ 'has-error' : errors.has('comment') || errors.comment}">
            <label class="control-label">Comment</label>
            <tiny-mce id="description" v-model="comment"></tiny-mce>
            <form-error v-if="errors.has('comment') || errors.comment" :errors="errors">
                {{ errors.first('comment') }}
            </form-error>
        </div>
        <div class="modal-footer">
            <vue-simple-spinner v-if="loading"></vue-simple-spinner>
            <button class="btn btn-default" v-on:click="clearBox()"> Cancel </button>
            <button type="submit" class="btn btn-primary" :disable="loading"><i class="fa fa-paper-plane"></i> Comment </button>
        </div>
    </form>
</template>

<script>
    import Helpers from '../helpers'
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";

    export default {
        components: {VueSimpleSpinner},
        props: [ 'ref_id', 'ref_type', 'reply_id' ],
        data() {
            return {
                comment: '',
                user: App.User,
                loading: false,
                alert: {}
            }
        },
        mounted: function() {

        },

        methods: {
            submit() {
                this.loading = true;
                let url = Helpers.auth_url('/api/comments/new', App.User)

                axios.post(url, {
                    ref_id: this.ref_id,
                    ref_type: this.ref_type,
                    comment: this.comment,
                    reply_id: this.reply_id
                }).then( (response) => {
                    this.loading = false;
                    bus.$emit('comment-posted', response.data)
                    this.clearBox()
                    this.renderComment(response.data)
                }).catch( (error) => {
                    if(error.response){
                        let data  = error.response.data;
                        this.alert = { type: 'danger', message: data.message}
                        this.loading = false;
                    }
                })
            },
            clearBox() {
                this.comment  = '';
            },
            renderComment(comment) {
                const html = '<div class="media" id="comment_item_' + comment.pk + '"><div class="media-left"><a href="#"><img src=""> </a> </div><div class="media-body"><h4 class="media-heading">' + comment.author.full_name + '</h4>' + comment.notes + '</div></div>';
                document.getElementById('comments-box').prepend(html)
                this.loading =false
            }
        }
    }
</script>