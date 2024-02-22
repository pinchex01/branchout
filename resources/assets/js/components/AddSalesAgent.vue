<template>
    <div class="agent-search-holder">
        <div class="form-group">
            <div class="input-group stylish-input-group">
                <input type="text" name="q" v-model="q" class="form-control"  placeholder="Search e.g Jane Doe" v-on:keydown="searchAgents($event.target.value)" >
                <span class="input-group-addon">
                                    <button type="submit">
                                        <span class="fa fa-search"></span>
                                    </button>
                                </span>
            </div>
        </div>
        <ul v-if="agents" class="list-group" id="contact-list">
            <li v-for="agent in agents" :key="agent.id">
                <div class="col-xs-12 col-sm-3 listrap-toggle">
                    <span></span>
                    <img v-bind:src="agent.avatar" v-bind:alt="agent.name" class="img-responsive img-circle" height="130" width="130" />
                </div>
                <div class="col-xs-12 col-sm-9">

                    <div class="col-md-10">
                        <span class="name">{{ agent.name }}</span> <br>
                        <span class="glyphicon glyphicon-earphone text-muted c-info" data-toggle="tooltip" :title="agent.phone"></span>
                        <span class="visible-xs"> <span class="text-muted">{{ agent.phone }}</span><br/></span>
                        <span class="fa fa-comments text-muted c-info" data-toggle="tooltip" :title="agent.email"></span>
                        <span class="visible-xs"> <span class="text-muted">{{ agent.email }}</span><br/></span>

                    </div>
                    <div class="col-md-2">
                        <span style="vertical-align: middle"><button class="btn btn-primary" v-on:click="addAgent(agent.id)"><i :class="{}"></i> add</button></span>
                    </div>

                </div>
                <div class="clearfix"></div>
            </li>
        </ul>
        <vue-simple-spinner size="massive" v-if="loading"></vue-simple-spinner>
    </div>
</template>
<script>
    import Helpers from '../helpers'
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";
    export default {
        components: {VueSimpleSpinner},
        props: [ 'organiser', 'event'],
        mounted() {

        },
        data() {
            return {
                q: '',
                loading: false,
                agents: [],
                submitting: false
            }
        },
        watch: {

        },
        methods: {
            searchAgents(q) {
                console.log("Query: ", q)
                this.loading = true;
                if (!this.q.length){
                    this.loading = false;
                    this.agents = [];
                    return;
                }
                axios.get('/api/search/agents?q=' + this.q)
                    .then((response) => {
                        this.agents = response.data.agents
                        this.loading = false;
                    })
                    .catch((error) => {
                        let $this = this;
                        if (error.response) {
                            this.errors = error.response.data;
                        }
                    });

            },
            addAgent(pk) {
                //this.loading = true;
                var url  = Helpers.auth_url('/api/events/' + this.event + '/add-sales-agent', App.User)
                axios.post( url, {  agent_id: pk })
                    .then((response) => {
                        console.log("Add Agent: ", response.data)
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