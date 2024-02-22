<template>
    <div>
        <div class="note note-warning" v-if="event.status != 'published'">
            This event is not visible to the public. Publish event to make it visible
            <button v-on:click="setEventStatus()" class="btn btn-info" id="btn-publish"> Publish Event</button>
        </div>
        <div class="note note-success" v-if="show_success">
            Event has been published successfully! It is now visible to the public...
        </div>
    </div>
</template>

<script>

    import Helpers from '../helpers'

    export default {
        mounted() {
            this.event = App.Event;
        },
        data() {
            return {
                loading: false,
                event: {},
                errors: [],
                show_success: false
            }
        },
        methods: {
            setEventStatus() {
                var url = Helpers.auth_url('/api/events/' + App.Event.id + '/change-status', App.User)
                axios.post(url, {status: 'published'})
                    .then((response) => {
                        //location.reload()
                        this.event = response.data;
                        this.show_success = true;
                        setTimeout((response) => {
                            this.show_success = false
                        },2000)
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