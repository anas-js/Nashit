<?php

namespace App\Http\Requests\BoardsController;

use App\Http\Requests\BaseReq;

class SettingsReq extends BaseReq
{

    public function rules(): array
    {
        return [
            "delete" => ["boolean"],
            "name" => ["string","between:1,20"],
            "private" => ["boolean"],
            "notifs" => ["boolean"],
        ];
    }
}
