<?php

namespace App\Http\Requests\BoardsController;

use App\Http\Requests\BaseReq;

class CreateReq extends BaseReq
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["required","string","between:1,20"],
        ];
    }
}
