<?php

namespace App\Http\Controllers;

use App\Http\Requests\HooksController\SettingsReq;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class HooksController extends Controller
{
    function settings(SettingsReq $request)
    {
        $key = $request->key;
        $user = User::find($request->user);
        if (!$user || !$user->checkHookKey($key)) {
            throw new HttpResponseException(response()->json(['message' => 'Unauthorized Access'], 401));
        }

        $settings = $request->settings;

        if (isset($settings['delete']) && $settings['delete']) {
            $user->info()->delete();
            $user->lessons()->delete();
            $courses = $user->courses()->get();
            foreach ($courses as $course) {
                if (Storage::disk('courses')->exists($course->id . '.jpg')) {
                    Storage::disk('courses')->delete($course->id . '.jpg');
                }
                $course->delete();
            }

            $boards =  $user->boards()->get();
            foreach ($boards as $board) {
                $board->tasks()->delete();
                if (Storage::disk('boards')->exists($board->id . '.jpg')) {
                    Storage::disk('boards')->delete($board->id . '.jpg');
                }
            }
            $user->lists()->delete();
            $user->boards()->delete();

            return ['done' => 'ok'];
        }


        if (isset($settings['timezone']) && $settings['timezone']) {
            $timezone = $settings['timezone'];
            try {
                now($timezone);

                $user->courses()->update([
                    'date_finish' => DB::raw("CONVERT_TZ(date_finish,'$timezone','$user->timezone')"),
                    'date_done' => DB::raw("CONVERT_TZ(date_done,'$timezone','$user->timezone')")
                ]);


                $user->lessons()->update([
                    'lessons.exp_date_done' => DB::raw("CONVERT_TZ(lessons.exp_date_done,'$timezone','$user->timezone')"),
                    'lessons.last_exp_date' => DB::raw("CONVERT_TZ(lessons.last_exp_date,'$timezone','$user->timezone')"),
                    'lessons.date_done' => DB::raw("CONVERT_TZ(lessons.date_done,'$timezone','$user->timezone')")
                ]);

            } catch (Exception $e) {
            }
        }

        // $notifs = $request->settings['notifs'];
        if (isset($settings['notifs'])) {
            $user->courses()->update(['notifs' => $settings['notifs']]);
            $user->boards()->update(['notifs' => $settings['notifs']]);
        }

        return ["done" => 'ok'];
        // notfis(true,fasle) => (open/close) notfis for all course and boards

    }
}
