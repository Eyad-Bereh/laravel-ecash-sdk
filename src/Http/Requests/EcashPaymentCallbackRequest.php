<?php

namespace IXCoders\LaravelEcash\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EcashPaymentCallbackRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = config("laravel-ecash-sdk.callback_validation_rules");
        return $rules;
    }
}
