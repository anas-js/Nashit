<?php

namespace App\Http\Requests\CoursesController;

use App\Http\Requests\BaseReq;
use Illuminate\Support\Facades\Auth;

class SettingsReq extends BaseReq
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
            "delete" => ["boolean"],
            "name" => ["string","between:1,20"],
            "private" => ["boolean"],
            "notifs" => ["boolean"],
            "weekend"=> ["array","between:0,6"],
            "weekend.*"=> ["required","integer","numeric","between:0,6",'distinct'],
            "done_days" => ["integer","between:0,{$user->limits('days_done_limit')}","numeric"],
        ];
    }
}
