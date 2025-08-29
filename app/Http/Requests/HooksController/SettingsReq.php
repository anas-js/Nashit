<?php

namespace App\Http\Requests\HooksController;

use App\Http\Requests\BaseReq;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SettingsReq extends BaseReq
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['required', 'string'],
            'user' => ['required', 'integer'],
            'settings' => ['required','array',"max:3"],
            'settings.timezone' => ['string'],
            'settings.delete' => ['boolean'],
            'settings.notifs' => ['boolean']
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['message'=>'Unauthorized Access'],401));
    }
}
