<template>
    <div class="form-group"  v-bind:class="{'has-error': errors.has('phone') || errors.name }" >
        <label v-if="label" class="control-label">{{ label }}</label>
        <input id="phone-input" type="text" name="phone" v-model="number" v-validate="'required|full_phone'" class="form-control phone-input"se>
        <form-error v-if="errors.has('phone') || errors.phone" :errors="errors">
            {{ errors.first('phone') }}
        </form-error>
    </div>
</template>

<script>
    import VeeValidate from 'vee-validate';

    export default {
        props: ['phone_number', 'label', 'name'],
        mounted() {
            $(function () {

                VeeValidate.Validator.extend('full_phone', {
                    getMessage: field => 'Invalid phone number',
                    validate: value => $('#phone-input').intlTelInput('isValidNumber')
                });

                $('#phone-input').intlTelInput({
                    nationalMode: false,
                    initialCountry: "auto",
                    geoIpLookup: function(callback) {
                        $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                            var countryCode = (resp && resp.country) ? resp.country : "";
                            callback(countryCode);
                        });
                    },
                    separateDialCode: false,
                    autoPlaceholder: "aggressive",
                    formatOnDisplay: true,
                    utilsScript: "/plugins/utils.js"
                });
            })

        },
        data() {
            return {
                number: '',
                full_phone: '',
                code: ''
            }
        },
        watch: {
            number(value) {
                this.setPhoneNumber();
            },
        },
        created() {

        },
        methods: {
            setPhoneNumber() {
                var number  = event.target.value;
                
                if ($('#phone-input').intlTelInput('isValidNumber')){
                    this.full_phone = $('#phone-input').intlTelInput('getNumber');
                    bus.$emit('int-phone-number',{
                        code: this.code,
                        number: this.number,
                        full: this.full_phone
                    })
                    if (this.name){
                        bus.$emit(this.name,{
                            code: this.code,
                            number: this.number,
                            full: this.full_phone
                        })
                    }
                    
                }
                bus.$emit('input',this.full_phone)
                

            }
        }
    }
</script>