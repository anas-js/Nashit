<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserController\setPrefsReq;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function registration(Request $request)
    {
        $user = $request->user();

        if (!$user->isRegistered()) {
            $user->registration();
        }

        return ["msg" => "ok"];
    }

    function limits(Request $request)
    {
        $user = $request->user();

        return [
            "limits" =>  $user->limits(select:['boards_limit','courses_limit','lessons_limit','days_done_limit']),
            "courses_number" => $user->courses()->count(),
            "boards_number" => $user->boards()->count()
        ];
    }
}
