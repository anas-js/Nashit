<?php

namespace App\Http\Requests\LessonsController;

use App\Http\Requests\BaseReq;

class setReq extends BaseReq
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            "set" => ["required","boolean"],
        ];
    }
}
