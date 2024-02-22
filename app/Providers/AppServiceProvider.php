<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Schema::defaultStringLength(191);
        \Validator::extend('full_phone', function($attribute, $value, $parameters, $validator) {
            try {
                //ensure phone number is numeric
                if (!is_numeric($value)) return false;

                $code_phone_no = encode_phone_number($value);
                return strlen($code_phone_no) == 12;
            } catch (\Exception $ex)
            {
                return false;
            }
        });

        \Validator::extend('phone', function($attribute, $value, $parameters, $validator) {
            try {
                $code = $parameters[0] ? request($parameters[0],$parameters[0]) : '254';

                //ensure code is numeric
                if (!is_numeric($code)) return false;

                //ensure phone number is numeric
                if (!is_numeric($value)) return false;

                $code_phone_no = encode_phone_number($value,$code);
                return strlen($code_phone_no) == 12;
            } catch (\Exception $ex)
            {
                return false;
            }
        });

        \Validator::extend('title', function($attribute, $value, $parameters, $validator) {
            return !preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $value);
        });
        \Validator::extend('ccn', function($attribute, $value, $parameters, $validator) {
            return CreditCard::validCreditCard(str_replace(' ','',$value))['valid'];
        });

        \Validator::extend('ccd', function($attribute, $value, $parameters, $validator) {
            try {
                $value  = str_replace(' ','',$value);
                $value = explode('/', $value);
                return CreditCard::validDate(strlen($value[1]) == 2 ? (2000+$value[1]) : $value[1], $value[0]);
            } catch(\Exception $e) {
                return false;
            }
        });

        \Validator::extend('cvc', function($attribute, $value, $parameters, $validator) {
            return ctype_digit($value) && (strlen($value) == 3 || strlen($value) == 4);
        });

        //Ensure value is the same as
        \Validator::extend('is', function($attribute, $value, $parameters, $validator) {
            try {
                $rhs  = $parameters[0];
            } catch (ValidationException $e){
                throw $e;
            }

            return $value == $rhs;
        });

        \Validator::extend('greater_than_field', function($attribute, $value, $parameters, $validator) {
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = $data[$min_field];
            return $value > $min_value;
        });

        \Validator::replacer('greater_than_field', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });

        \Validator::extend('valid_id_number', function($attribute, $value, $parameters, $validator) {
            $validate_with = $parameters[0];
            $data = $validator->getData();
            $user_data = get_user_by_id_number($value);
            if(!$user_data) return false;
            return strtolower($user_data->first_name) === strtolower($data[$validate_with]);
        });

        \Validator::replacer('valid_id_number', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });

        \Validator::extend('temp_file_exists', function($attribute, $value, $parameters, $validator) {
           
            return \Storage::disk('local')->exists($value);
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
