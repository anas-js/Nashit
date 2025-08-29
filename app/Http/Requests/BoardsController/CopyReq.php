<?php

namespace App\Http\Requests\BoardsController;

use App\Http\Requests\BaseReq;
use Illuminate\Foundation\Http\FormRequest;

class CopyReq extends BaseReq
{

    public function rules(): array
    {
        return [
            "type" => ["required","string","in:with-tasks,without-tasks"],
        ];
    }
}
