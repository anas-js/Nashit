<?php

namespace App\Http\Requests\ListController;

use App\Http\Requests\BaseReq;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
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
            "lists" => ["required","array","between:1,{$user->limits('lists_limit')}"],
            "lists.*.id" => ["required","numeric","integer","distinct"],
            // "lists.*.name" => ["required","string","between:1,20"],
            "lists.*.tasks" => ["array","between:0,{$user->limits('tasks_limit')}"],
            "lists.*.tasks.*.id" => ["required","numeric","integer","distinct"],
            "lists.*.tasks.*.done" => ["required","boolean"],
            "lists.*.tasks.*.name" => ["required","string","between:1,50"],
            "lists.*.tasks.*.note" => ["nullable","string","max:{$user->limits('note_limit')}",function (string $attribute, mixed $value, Closure $fail) use ($user) {
                $isString = gettype($value) === "string";
                if(!$isString) {
                    return;
                }
                if(strlen($value) > $user->limits('note_limit')) {
                    $fail("The {$attribute} field must not be greater than 10000 Byte");
                }
            }],
            "lists.*.tasks.*.process" => ["array","max:4"],
            "lists.*.tasks.*.process.name" => ["date_format:Y-m-d\TH:i:s.u\Z"],
            "lists.*.tasks.*.process.note" => ["date_format:Y-m-d\TH:i:s.u\Z"],
            "lists.*.tasks.*.process.move" => ["date_format:Y-m-d\TH:i:s.u\Z"],
            "lists.*.tasks.*.process.done" => ["date_format:Y-m-d\TH:i:s.u\Z"],
            "lists.*.tasks.*.process.remove" => ["date_format:Y-m-d\TH:i:s.u\Z"],
            // "lists.*.process" => ["array","max:4"],
            // "lists.*.process.create" => ["date_format:Y-m-d\TH:i:s.u\Z"],
            // "lists.*.process.rename" => ["date_format:Y-m-d\TH:i:s.u\Z"],
            // "lists.*.process.remove" => ["date_format:Y-m-d\TH:i:s.u\Z"],
            // "lists.*.process.reindexed" => ["date_format:Y-m-d\TH:i:s.u\Z"],
        ];
    }
}
