<template>
    <span>
        <div class="alert icon-alert alert-danger clearfix" v-if="type == 'danger'">
            <span class="fa fa-info alert-icon"></span>
            <div class="alert-body">
                <slot></slot>
            </div>
        </div>
        <div class="alert icon-alert alert-success clearfix" v-if="type == 'success'">
            <span class="fa fa-info alert-icon"></span>
            <div class="alert-body">
                <slot></slot>
            </div>
        </div>
        <div class="alert icon-alert alert-info clearfix" v-if="type == 'info'">
            <span class="fa fa-info alert-icon"></span>
            <div class="alert-body">
                <slot></slot>
            </div>
        </div>
        <div class="alert icon-alert alert-warning clearfix" v-if="type == 'warning'">
            <span class="fa fa-info alert-icon"></span>
            <div class="alert-body">
                <slot></slot>
            </div>
        </div>
        <div class="alert icon-alert alert-primary clearfix" v-if="type == 'primary'">
            <span class="fa fa-info alert-icon"></span>
            <div class="alert-body">
                <slot></slot>
            </div>
        </div>
    </span>
</template>
<script>
    export default {
        props: ["type", "dismissible"],
        mounted() {

        },
        data() {
            return {

            }
        },
        methods: {

        }
    }
</script>