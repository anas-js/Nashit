<?php

namespace App\Http\Requests\TaskController;

use App\Http\Requests\BaseReq;

class MoveReq extends BaseReq
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "to" => ["required","numeric","integer"],
        ];
    }
}
