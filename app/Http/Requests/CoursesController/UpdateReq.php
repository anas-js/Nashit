<?php

namespace App\Http\Requests\CoursesController;

use Closure;
use App\Http\Requests\BaseReq;
use Illuminate\Support\Facades\Auth;

class UpdateReq extends BaseReq
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
            "lessons.*.id" => ["required","numeric","integer"],
            "lessons.*.done" => ["required","boolean"],
            "lessons.*.name" => ["required","string","between:1,50"],
            "lessons.*.note" => ["nullable","string","max:{$user->limits('note_limit')}",function (string $attribute, mixed $value, Closure $fail) use ($user) {
                $isString = gettype($value) === "string";
                if(!$isString) {
                    return;
                }
                if(strlen($value) > $user->limits('note_limit')) {
                    $fail("The {$attribute} field must not be greater than {$user->limits('note_limit')} Byte");
                }
            }],
            "lessons.*.process" => ["array","max:3"],
            "lessons.*.process.name" => ["date_format:Y-m-d\TH:i:s.u\Z"],
            "lessons.*.process.note" => ["date_format:Y-m-d\TH:i:s.u\Z"],
            "lessons.*.process.set" => ["date_format:Y-m-d\TH:i:s.u\Z"],
        ];
    }
}
