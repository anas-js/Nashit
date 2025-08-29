<?php

namespace App\Console\Commands;

use App\Helpers\DateConv;
use App\Mail\SummaryEmail;
use App\Models\Course\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class emailSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Sending a summary of today's lessons at 8 a.m. to each user";

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //    $this->info($d);

        // , function (Builder $query) {
        //     $query->from('dbs13191347.registered');
        // })




        User::with('info')->with('prefs')->chunk(50, function (Collection $users) {
            foreach ($users as $user) {
                if (isset($user->info) && (now($user->timezone)->format('H') == 8)) {
                    $courses = $user->courses()->where('finish', false)->where('notifs', true)->whereHas('lessons', function ($query) use ($user) {
                        $query->where(function ($q) use ($user) {
                            $q->whereRaw("DATE(exp_date_done) = DATE(CONVERT_TZ(now(),'-04:00','$user->timezone'))")->orWhere(function ($q) use ($user) {
                                $q->whereRaw("DATE(exp_date_done) < DATE(CONVERT_TZ(now(),'-04:00','$user->timezone'))")->where('done', false);
                            });
                        });
                    })->with(['lessons' => function ($query) use ($user) {
                        $query->where(function ($q) use ($user) {
                            $q->whereRaw("DATE(exp_date_done) = DATE(CONVERT_TZ(now(),'-04:00','$user->timezone'))")->orWhere(function ($q) use ($user) {
                                $q->whereRaw("DATE(exp_date_done) < DATE(CONVERT_TZ(now(),'-04:00','$user->timezone'))")->where('done', false);
                            });
                        });
                    }])->get();


                    if ($courses->count() > 0) {
                        if ($user->prefs->lang !== 'auto') {
                            App::setLocale($user->prefs->lang);
                        };



                        Mail::to($user)->send(new SummaryEmail($user, $courses));
                    }
                }
            }
        });

        // dd($users->toArray());




        // User::whereHas('info', function (Builder $query) {
        //     $query->from('dbs13191347.registered');
        // })->whereHas('courses', function (Builder $query) {
        //     $query->from('dbs13191347.courses')->where('finish', false)->where('notifs', true)->whereHas('lessons', function ($query) {
        //         $query->from('dbs13191347.lessons')->where(function ($q) {
        //             $q->whereRaw("DATE(exp_date_done) = DATE(CONVERT_TZ(now(),'-04:00',users.timezone))")->orWhere(function ($q) {
        //                 $q->whereRaw("DATE(exp_date_done) < DATE(CONVERT_TZ(now(),'-04:00',users.timezone))")->where('done', false);
        //             });
        //         });
        //     });
        // })->whereRaw('DATE_FORMAT(CONVERT_TZ(now(),"-04:00",users.timezone),"%H") = 1')->with('prefs')->chunk(50, function (Collection $users) {


        //     foreach ($users as $user) {
        //         $courses = $user->courses()->where('finish', false)->where('notifs', true)->whereHas('lessons', function ($query) use ($user) {
        //             $query->where(function ($q) use ($user) {
        //                 $q->whereRaw("DATE(exp_date_done) = DATE(CONVERT_TZ(now(),'-04:00','$user->timezone'))")->orWhere(function ($q) use ($user) {
        //                     $q->whereRaw("DATE(exp_date_done) < DATE(CONVERT_TZ(now(),'-04:00','$user->timezone'))")->where('done', false);
        //                 });
        //             });
        //         })->with(['lessons' => function ($query) use ($user) {
        //             $query->where(function ($q) use ($user) {
        //                 $q->whereRaw("DATE(exp_date_done) = DATE(CONVERT_TZ(now(),'-04:00','$user->timezone'))")->orWhere(function ($q) use ($user) {
        //                     $q->whereRaw("DATE(exp_date_done) < DATE(CONVERT_TZ(now(),'-04:00','$user->timezone'))")->where('done', false);
        //                 });
        //             });
        //         }])->get();



        //         if ($user->prefs->lang !== 'auto') {
        //             App::setLocale($user->prefs->lang);
        //         };



        //         Mail::to($user)->send(new SummaryEmail($user, $courses));
        //     }
        // });

        // $this->info('send');
    }
}
