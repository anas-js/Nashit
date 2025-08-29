<?php

namespace App\Http\Requests\CoursesController;

use App\Http\Requests\BaseReq;
use Illuminate\Support\Facades\Auth;

class CreateReq extends BaseReq
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // dd();
        // Auth::user()

        $user = Auth::user();

        return [
            "name" => ["required","string","between:1,20"],
            "lessons" => ["required","array","between:1,{$user->limits('lessons_limit')}"],
            "inSpace" => ["required","boolean"],
            "lessons.*.name" => ["required","string","between:1,50"],
            "done_days" => ["required","integer","between:1,{$user->limits('days_done_limit')}","numeric"],
            "weekend"=> ["array","between:0,6"],
            "weekend.*"=> ["required","integer","numeric","between:0,6",'distinct'],
        ];
    }
}
