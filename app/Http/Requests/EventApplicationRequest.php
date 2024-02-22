<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventApplicationRequest extends FormRequest
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
            'name' => "required",
            'location' => "required",
            'start_date' => "required|date|after:yesterday",
            'end_date' => "nullable|date|after_or_equal:start_date",
            'on_sale_date' => "sometimes",
            'organiser_id' => "required|exists:organisers,id",
            'user_id' => "required|exists:users,id",
            'avatar' => "required",
            'description' => "required",
            'bank_account_id' => "required|exists:bank_accounts,id"
        ];
    }
}
