<?php

namespace App\Http\Requests\LessonsController;

use Closure;
use App\Http\Requests\BaseReq;
use Illuminate\Support\Facades\Auth;

class noteReq extends BaseReq
{

    public function rules(): array
    {

        $user = Auth::user();
        return [
            "returnSmallNote" => "boolean",
            "note" => ["nullable","string","max:{$user->limits('note_limit')}",function (string $attribute, mixed $value, Closure $fail) use ($user) {
                $isString = gettype($value) === "string";
                if(!$isString) {
                    return;
                }
                if(strlen($value) > $user->limits('note_limit')) {
                    $fail("The {$attribute} field must not be greater than {$user->limits('note_limit')} Byte");
                }
            }],
        ];
    }
}
