<?php

namespace App\Http\Requests\CoursesController;

use App\Http\Requests\BaseReq;
use Illuminate\Support\Facades\Auth;

class AddReq extends BaseReq
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $user = Auth::user();
        return [
            "lessons" => ["required","array","between:1,{$user->limits('lessons_limit')}"],
            "lessons.*.name" => ["required","string","between:1,50"],
            "lessons.*.id" => ["prohibited"]
        ];
    }
}
