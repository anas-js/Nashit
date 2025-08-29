<?php

namespace App\Http\Requests\TaskController;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseReq;

class ChangeReq extends BaseReq
{
    public function rules(): array
    {
        return [
        //   "from" => ["required","numeric"],
          "to" => ["required","numeric","integer"],
        //   "oldIndex" => ["required","integer","between:0,49"],
          "newIndex" => ["required","numeric","integer","between:0,49"]
        ];
    }
}
