<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Example Component</div>

                    <div class="panel-body">
                        // this will be displayed upon successful submission of form
                        <div class="alert alert-success" v-if="submitted">
                            Post created!
                        </div>
                        <form>
                            <div class="form-group">
                                <label class="control-label">ID Number</label>
                                <input type="text" name="id_number" v-bind="id_number" class="form-control">
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
