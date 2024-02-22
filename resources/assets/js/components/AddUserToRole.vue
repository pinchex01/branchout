<template>
  <form v-loading.body="loading" action="" method="post" v-on:submit.prevent="submit()">
      <alert-message v-if="alert.hasOwnProperty('message')" :type="alert.type">{{ alert.message }}</alert-message>
      <find-user-form></find-user-form>
      <div v-if="!!user.id_number" class="form-group" v-bind:class="{ 'has-error' : errors.has('role_id') }">
          <label class="control-label">Role</label>
          <select name="role" v-model="role_id" class="form-control" v-validate="'required'">
              <option v-bind:value="''">Selct Role</option>
              <option v-for="role in roles" v-bind:value="role.id">
              {{ role.name }}
              </option>
          </select>
          <form-error v-if="errors.has('role_id')" :errors="errors">
              {{ errors.first('role_id') }}
          </form-error>
      </div>
      <div class="modal-footer" v-if="!!user.id_number">
          <vue-simple-spinner v-if="loading"> Thinking </vue-simple-spinner>
          <button type="submit" class="btn btn-primary" :disabled="loading"> Submit</button>
      </div>
    </form>
</template>

<script>
    import Helpers from '../helpers'
    import VueSimpleSpinner from "../../../../node_modules/vue-simple-spinner/src/components/Spinner";

    export default {
        props: [ "type", "merchant_id" ],
        components: {VueSimpleSpinner},
        mounted() {

        },
        data() {
            return {
                loading: false,
                user: {
                    id_number: '',
                    first_name: '',
                    phone: '',
                    email: '',
                    registered: 0,
                    pk: null
                },
                alert: {},
                roles: App.frmRoles,
                role_id: ''
            }
        },

        methods: {
            submit() {
                this.loading = true
                var url  = '/api/users/add-role'
                axios.post(Helpers.auth_url(url, App.User), {
                    user_id: this.user.pk,
                    role_id: this.role_id,
                    type: this.type ? this.type : 'sys',
                    merchant_id: this.merchant_id
                }).then( (response) => {
                    this.$notify.success({
                      title: "Success",
                      message : response.data.message
                    })
                    Helpers.reload_after(1000)
                }).catch((error) => {

                    this.loading = false;
                    if (error.response.status  == 422) {
                        this.get_server_errors(error.response.data);
                    }

                    this.$notify.error({
                      title: "Oooops",
                      message : "An error occcurred"
                    })
                });
            },
            get_server_errors(errors){
                for(var key in errors) {
                    if (errors.hasOwnProperty(key)){
                        this.$validator.errorBag.add(key, errors[key][0])
                    }
                }

            }
        },
        created() {
          bus.$on('userFound',(msg) => {
              if (msg){
                  Object.assign(this.user, msg)
              }else{
                  this.user  = {}
              }
          })
        }
    }
</script>
