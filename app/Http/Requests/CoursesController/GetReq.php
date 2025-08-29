<?php

namespace App\Http\Requests\CoursesController;

use App\Http\Requests\BaseReq;

class GetReq extends BaseReq
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "order" => ["string","in:old-new,new-old,last-active,ratio"],
            "type" => ["array","between:1,2"],
            "type.*" => ["in:done,not-done"]
        ];
    }
}
