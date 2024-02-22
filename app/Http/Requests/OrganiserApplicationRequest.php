<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganiserApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'organiser.name' => "required",
            'organiser.email' => "required|email",
            'organiser.phone' => "required|full_phone",
            'bank_account.account_type' => "required|in:bank,paybill",
            'bank_account.name' => "required|min:3",
            'bank_account.account_no' => "required|confirmed|numeric|min:5",
            'bank_account.account_no_confirmation' => "required",
            'bank_account.bank_id' => "sometimes|required_if:bank_account.account_type,bank"
            
        ];
    }

    public function attributes()
    {
        return [
            'bank_account.account_type' => "bank_account_type",
            'bank_account.bank_id' => "bank_id",
            'bank_account.account_name' => "bank_account_name",
            'bank_account.account_no' => "bank_account_no",
            'bank_account.account_no_confirmation' => "bank_account_no_confirmation"
        ];
    }
}
