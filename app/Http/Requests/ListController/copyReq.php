<?php

namespace App\Http\Requests\ListController;

use App\Http\Requests\BaseReq;
use Illuminate\Foundation\Http\FormRequest;

class copyReq extends BaseReq
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
            "type" => ["required","string","in:with-tasks,without-tasks"],
        ];
    }
}
