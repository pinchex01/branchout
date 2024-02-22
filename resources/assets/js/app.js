/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.bus = new Vue({});
Vue.use(require('vuex'));

//bootstrap vue validate
import VeeValidate, { Validator } from 'vee-validate';
import moment from 'moment';
import ElementUI from 'element-ui'
import tinymce from 'tinymce';
import 'tinymce/themes/modern/theme'
import TinyMCE from 'tinymce-vue-2'


import locale from 'element-ui/lib/locale/lang/en'
import 'element-ui/lib/theme-default/index.css'
require( './custom-validators')

Vue.use(ElementUI, {locale})
Validator.installDateTimeValidators(moment);

Vue.use(VeeValidate);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('form-error', require('./components/FormError.vue'));
Vue.component('alert-message', require('./components/AlertMessage.vue'));
Vue.component('user-registration-form', require('./components/UserRegistrationForm.vue'));
Vue.component('login-form', require('./components/LoginForm.vue'));
Vue.component('organiser-form', require('./components/OrganiserForm.vue'));
Vue.component('event-form', require('./components/EventForm.vue'));
Vue.component('ticket-form', require('./components/TicketForm.vue'));
Vue.component('bank-account-form', require('./components/BankAccountForm.vue'));
Vue.component('event-status-notification', require('./components/EventStatusNotification.vue'));
Vue.component('user-search-form', require('./components/UserSearchForm.vue'));
Vue.component('add-sales-person-form', require('./components/AddSalesPerson.vue'));
Vue.component('phone-input-field', require('./components/PhoneInputField.vue'));
Vue.component('organiser-application-form', require('./components/OrganiserApplicationForm.vue'));
Vue.component('file-upload-input', require('./components/FileUploadField.vue'));
Vue.component('event-application-form', require('./components/EventApplicationForm.vue'));
Vue.component('sales-agent-application-form', require('./components/SalesAgentApplicationForm.vue'));
Vue.component('add-sales-agent-form', require('./components/AddSalesAgent.vue'));
Vue.component('user-auth-form', require('./components/AuthComboForm.vue'));
Vue.component('buy-event-tickets-form', require('./components/BuyTicketForm.vue'));
Vue.component('order-details-form', require('./components/OrderDetailsForm.vue'));
Vue.component('checkout-form', require('./components/Checkout.vue'));
Vue.component('tiny-mce-editor',  require('./components/TinyMceEditor.vue'));
Vue.component('tiny-mce', TinyMCE)
Vue.component('comment-form',  require('./components/CommentForm.vue'));
Vue.component('manual-order-form',  require('./components/ManualOrderForm.vue'));
Vue.component('user-form',  require('./components/UserForm.vue'));
Vue.component('forgot-password-form',  require('./components/ForgotPassword.vue'));
Vue.component('find-user-form',  require('./components/FindUser.vue'));
Vue.component('user-role-form',  require('./components/AddUserToRole.vue'));

const app = new Vue({
    el: '#app'
});
