<?php

namespace App\Http\Controllers;

use App\Models\Update;
use Illuminate\Http\Request;

class GlobalContoller extends Controller
{
    function updates(Request $request) {
        return Update::orderBy('date','desc')->get();
    }
}
