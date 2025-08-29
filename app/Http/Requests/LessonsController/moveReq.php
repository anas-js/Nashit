<?php

namespace App\Http\Requests\LessonsController;

use App\Http\Requests\BaseReq;
use Illuminate\Foundation\Http\FormRequest;
class moveReq extends BaseReq
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "before" => ["integer","numeric","integer","required_without:after"],
            "after" => ["integer","numeric","integer","required_without:before"],
        ];
    }
}
//
