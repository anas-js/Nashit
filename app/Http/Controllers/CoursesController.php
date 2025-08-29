<?php

namespace App\Http\Controllers;

use App\Helpers\DateConv;
use App\Helpers\ImageUploader;
use App\Http\Requests\CoursesController\AddReq;
use App\Http\Requests\CoursesController\CopyReq;
use App\Http\Requests\CoursesController\GetReq;
use App\Http\Requests\CoursesController\CreateReq;
use App\Http\Requests\CoursesController\SettingsReq;
use App\Http\Requests\CoursesController\UpdateReq;
use App\Http\Requests\CoursesController\UploadReq;
use App\Models\Course\Course;

use App\Models\Course\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class CoursesController extends Controller
{

    // =============== Pages ===============
    // Get All Courses For User
    public function get(GetReq $request)
    {
        $user = $request->user();
        $tz = $request->tz;
        if ($user) {
            $courses = $user->courses();
            $order = $request->order;
            $type = $request->type;
            if ($type) {
                if (count($type) === 1) {
                    $courses->where("finish", $type[0] === 'done');
                }
            }

            if ($order) {
                switch ($order) {
                    case ('new-old'): {
                            $courses->orderBy("created_at", "desc");
                            break;
                        }
                    case ('old-new'): {
                            $courses->orderBy("created_at", "asc");
                            break;
                        }
                    case ('last-active'): {
                            $courses->orderBy("last_activity", "desc");
                            break;
                        }
                    case ('ratio'): {
                            $courses->orderBy("ratio", "desc");
                            break;
                        }

                }
            } else {
                $courses->orderBy("created_at", "desc");
            }
        } else {
            $courses = $request->profile->courses()->where("private", false);
        }


        return $courses->simplePaginate(6)->through(function ($course) use ($tz) {
            $course["date_finish"] = Carbon::parse($course["date_finish"], $tz)->format("Y-m-d"); //*TZ* ->tz($tz)
            $course->makeVisible(['private']);
            return $course;
        });
    }
    // Course Create
    public function create(CreateReq $request, $return_course = false)
    {

        $user = $request->user();
        $courseLength = $user->courses()->count();
        if (($courseLength + 1) > $user->limits('courses_limit')) {
            throw ValidationException::withMessages([
                "courses" => "you account have {$user->limits('courses_limit')} course!!"
            ]);
        }
        // varible
        $lessons = $request->lessons;
        $lessonsLength = count($lessons);
        $weekend =  $request->weekend ?? [];
        $doneDays = $request->done_days;
        $inSpace = $request->inSpace;



        $date = Carbon::now($user->timezone);

        $dateToCalcWeekend = $date->copy();
        $result = $this->getWorkDays($inSpace, $doneDays, $dateToCalcWeekend, $weekend); //!!!!!!!!!

        if ($result['workDays'] <= 0) {
            throw ValidationException::withMessages([
                "weekend" => "You want to complete the course in $doneDays days, and the next $doneDays days are the weekends."
            ]);
        }

        $this->setLessonsDays($lessons, $result['workDays'], $weekend, $inSpace, $date);



        $course = $user->courses()->create([
            "name" => htmlspecialchars($request->name),
            "done_days" => $doneDays,
            "lessons_number" => $lessonsLength,
            'date_finish' =>  $date->addDay($result['total'] - 1),
            "weekend" => $request->weekend,
            "inSpace" => $inSpace,
            "workDays" => $result['workDays'],
            'notifs' => $user->notifs_apps
        ]);


        $course->lessons()->createMany($lessons);

        if ($return_course) {
            return $course;
        }
        return ["id" => $course->id];
    }
    // is Course Finish  ->>>>> profile
    public function finish(Request $request, $id)
    {
        $user = $request->user();
        $tz = $request->tz;
        if ($user) {
            $course = $this->isExist($user, $id);
        } else {
            $profile = $request->profile;
            $course = $this->IsAvailable($profile, $id);
        }




        $startCourse = Carbon::parse($course->created_at)->tz($tz)->startOfDay();
        $endCourse =  Carbon::parse($course->date_done, $tz)->startOfDay(); //*TZ* ->tz($tz)
        $dateLeft = $startCourse->diffInDays($endCourse) + 1;




        if ($course->finish) {
            $data = [
                "course" => [
                    "startDate" => $startCourse->format("Y-m-d"),
                    "endDate" => [
                        // DateConv::dateConvert($course->created_at, $course->date_finish, $user->timezone)
                        "exp" => Carbon::parse($course["date_finish"], $tz)->format("Y-m-d"), //*TZ* ->tz($tz)

                        "date" => Carbon::parse($course->date_done, $tz)->format("Y-m-d") //*TZ* ->tz($tz)
                    ],
                    "dayLeft" => [
                        "value" => $dateLeft
                    ],
                    "lessons" => $course->lessons_number
                ],
                "end" => true
            ];

            if ($dateLeft > $course->done_days) {
                $data["course"]["dayLeft"]["notice"] = [
                    "type" => "-",
                    "value" => $dateLeft - $course->done_days
                ];
            } else if ($dateLeft < $course->done_days) {
                $data["course"]["dayLeft"]["notice"] = [
                    "type" => "+",
                    "value" => $course->done_days - $dateLeft
                ];

                $data["course"]["endDate"]["early"] = (100 / $course->done_days) * ($course->done_days - $dateLeft);
            }


            return $data;
        } else {
            return [
                "end" => false
            ];
        }
    }

    // set Settings For Course
    public function settings(SettingsReq $request, $id)
    {
        $user = $request->user();
        $course = $this->isExist($user, $id);
        $tz = $request->tz;


        if ($request->delete) {
            $course->lessons()->delete();
            if (Storage::disk('courses')->exists($course->id . '.jpg')) {
                Storage::disk('courses')->delete($course->id . '.jpg');
            }
            $course->delete();

            return [
                "msg" => "Course Deleted Successfully!"
            ];
        } else {
            if ($request->has("name")) {
                $request->merge(["name" => htmlspecialchars($request->name)]);
            }

            if ($request->has("weekend") && ($request->weekend != $course->weekend)) {
                $date = now($tz);
                $dateCourseCreate = Carbon::parse($course->created_at)->tz($user->timezone);

                $result = $this->getWorkDays($course->inSpace, $course->done_days, $dateCourseCreate, $request->weekend, $date);
                $resultFromStartCourse = $this->getWorkDays($course->inSpace, $course->done_days, $dateCourseCreate, $request->weekend);

                // dd($result);
                if ($course->inSpace && $result['workDays'] <= 0) {
                    throw new HttpResponseException(response()->json([
                        "weekend" => "There are no days left to complete {$result['workDays']}",
                        'code' => 3
                    ], 404));
                }

                if ($result['workDays'] > 0) {
                    $lessons = collect($this->redistLessonsDays($course, $date, customWeekend: $request->weekend));

                    $lessons->each(function ($lesson) use ($course) {
                        $course->lessons()->updateOrCreate(["id" => $lesson['id']], $lesson);
                    });
                }

                // $DaysAgo = $this->getDoneDays($dateCourseCreate, $date,weekend:$request->weekend,inSpace:$course->inSpace);
                $course->update([
                    'workDays' => $resultFromStartCourse['workDays'],
                    'weekend' => $request->weekend,
                    "date_finish" => $dateCourseCreate->addDay($result['total'] - 1)
                ]);
            }

            if ($request->has("done_days")) {
                $date = Carbon::now($tz);
                /// work days
                $doneDays = $request->done_days + 1;
                $inSpace = $course->inSpace;


                $dateCourseCreate = Carbon::parse($course->created_at)->tz($user->timezone);

                $weekend =  $course->weekend ?? [];


                $DaysAgo = $this->getDoneDays($dateCourseCreate, $date, $course);


                $doneDaysFromStart = $DaysAgo['doneDays'] + $doneDays;




                if ((($doneDaysFromStart) === $course->done_days)) {
                    return ["msg" => "Course Modified Successfully!"];
                }

                if (($doneDaysFromStart) > $user->limits('days_done_limit')) {
                    throw new HttpResponseException(response()->json([
                        "done_days" => "The limit of {$user->limits('days_done_limit')} days has been exceeded",
                        'code' => 4
                    ], 404));
                }


                // START CHINGER
                $result = $this->getWorkDays($inSpace, $doneDaysFromStart, $dateCourseCreate, $weekend, $date);





                if ($result['workDays'] <= 0) {
                    throw new HttpResponseException(response()->json([
                        "weekend" => "There are no days left to complete {$result['workDays']}",
                        'code' => 3
                    ], 404));
                }

                $lessons = collect($this->redistLessonsDays($course, $date, customDoneDays: $doneDaysFromStart));


                $lessons->each(function ($lesson) use ($course) {
                    $course->lessons()->updateOrCreate(["id" => $lesson['id']], $lesson);
                });



                $course->update([
                    'workDays' => $DaysAgo['workDays'] + $result['workDays'],
                    'done_days' => $doneDaysFromStart,
                    "date_finish" => $date->addDay($result['total'] - 1)
                ]);
            }

            if ($request->has("notifs")) {
                if ($request->notifs && !$user->notifs_apps) {
                    throw new HttpResponseException(response()->json([
                        'account' => 'Sending to email is turned off in the account settings',
                        'code' => 1
                    ], 404));
                }
                $course->update([
                    'notifs' => $request->notifs
                ]);
            }

            // $workDays
            $course->update($request->only("private", "name"));



            return [
                "msg" => "Course Modified Successfully!"
            ];
        }
    }

    // ->>>>> profile
    public function stats(Request $request, $id)
    {
        $user = $request->user();
        $tzUser = $request->tz;
        if ($user) {
            $course = $this->isExist($user, $id);
        } else {
            $profile = $request->profile;
            $course = $this->IsAvailable($profile, $id);
            $tzProfile = $request->profile->timezone;
        }

        $tz = $tzProfile ?? $tzUser;


        if ($course->finish) {
            return [
                "end" => true
            ];
        }




        $date = Carbon::now($tz)->startOfDay();




        $dateString = $date->format("Y-m-d");




        //* !!!!!!!!!!!!!!!!!!!
        $lessonsExpDone = $course->lessons()->whereDate('exp_date_done', "<=", $dateString)->where(function ($q) use ($dateString) {
            $q->whereDate('last_exp_date', '<=', $dateString)->orWhere('last_exp_date', Null);
        })->count();

        $lessonsExpForDay = $course->lessons()->whereDate('exp_date_done', "=", $dateString)->count();
        //* !!!!!!!!!!!!!!!!!!!

        //   return ['lessonsExpForDay'=>$lessonsExpForDay];


        // $currRatio = round(100 / $course->lessons_number * $doneLessons, 2);
        $expRatio = round(100 / $course->lessons_number * $lessonsExpDone, 2);
        $todayExpRatio = round(100 / $course->lessons_number * $lessonsExpForDay, 2);
        $currDay = $date->tz($tzUser)->diffInDays(Carbon::parse($course->created_at)->tz($tzUser)->startOfDay()) + 1;


        return [
            'finished' => $date->startOfDay()->greaterThan(Carbon::parse($course->date_finish, $tz)->startOfDay()),
            "ratio" => [
                "curr" => $course->ratio,
                "exp" => $expRatio,
                "todayExp" => $todayExpRatio
            ],
            "day" => [
                "full" => $course->done_days,
                "curr" => $currDay
            ]
        ];
    }

    // ->>>>> profile
    public function lessons(Request $request, $id)
    {
        $user = $request->user();
        $tzUser = $request->tz;
        if ($user) {
            $course = $this->isExist($user, $id);
        } else {
            $profile = $request->profile;
            $course = $this->IsAvailable($profile, $id);
            $tzProfile = $request->profile->timezone;
        }

        $tz = $tzProfile ?? $tzUser;

        // $course = $user->courses()->find($id);
        // if (!$course) {
        //     throw ValidationException::withMessages([
        //         "course" => 'course not found'
        //     ]);
        // }
        $lessons = $course->lessons()->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->simplePaginate(8)->through(function ($lesson) use ($tz, $tzUser) {
            $lesson = collect($lesson);
            $lesson["date"] = collect([
                // DateConv::dateConvert($course->created_at, $lesson['exp_date_done'], $user->timezone)
                "required" => Carbon::parse($lesson['exp_date_done'], $tz)->tz($tzUser)->format("Y-m-d"), //*TZ* ->tz($tz)
                "done" => null
            ]);

            if ($lesson['date_done']) {
                $lesson["date"]['done'] = Carbon::parse($lesson['date_done'], $tz)->tz($tzUser)->format("Y-m-d"); //*TZ* ->tz($tz)
            }



            $lesson["smallNote"] = mb_substr(strip_tags($lesson["note"]), 0, 100);

            return $lesson->except("course_id", "date_done", "exp_date_done", "index");
        });
        return [
            "lessons" => $lessons,
            "course" => [
                "end" => $course->finish,
                "lessons" => $course->lessons_number
            ]
        ];
    }

    // ->>>>> profile
    public function today(Request $request, $id)
    {
        $user = $request->user();
        $tzUser = $request->tz;
        if ($user) {
            $course = $this->isExist($user, $id);
        } else {
            $profile = $request->profile;
            $course = $this->IsAvailable($profile, $id);
            $tzProfile = $request->profile->timezone;
        }

        $tz = $tzProfile ?? $tzUser;

        if ($course->finish) {
            return [
                "end" => true
            ];
        }

        $date = Carbon::now($tz)->startOfDay();
        // Carbon::__callStatic('now');






        $dateString = $date->format("Y-m-d");

        // DateConv::fieldQueryConv($course->created_at, "exp_date_done", $user->timezone);
        // $query = DateConv::CONVERT_TZ("exp_date_done", $tz); //*TZ*
        // ($query = '$dateString') OR
        // $queryLessonsForDay = $course->lessons()->select("*")->selectRaw("$query as exp_date_done")->whereRaw("($query = '$dateString')")->whereRaw("(($query < '$dateString') AND (done = false))")->orderByRaw("DATE(exp_date_done) ASC, `index` ASC");
        // $query . "<= '$dateString'"

        // return ['r' => "($query = '$dateString') OR ($query < '$dateString' AND done = false)"];
        // return ["e" => "($query = '$dateString') OR (($query < '$dateString') AND (done = false))"];

        // $queryLessonsForDay = $course->lessons()->select("*")->selectRaw("$query as exp_date_done")->whereRaw("($query = '$dateString')")->orWhereRaw("(($query < '$dateString') AND (done = false))")->orderByRaw("DATE(exp_date_done) ASC, `index` ASC");

        //*TZ* $queryLessonsForDay = $course->lessons()->select("*")->selectRaw("$query as exp_date_done")->whereRaw("(($query = '$dateString') OR (($query < '$dateString') AND (done = false)))")->orderByRaw("DATE(exp_date_done) ASC, `index` ASC");

        // return  $queryLessonsForDay->get();

        // "((exp_date_done = '2020-01-01') OR ((exp_date_done < '2020-01-01') AND (done = false)))"
        // ->select("*")
        $lessonsForDay = $course->lessons()->where(function ($q) use ($dateString) {
            $q->whereDate('exp_date_done', $dateString)->orWhere(function ($q) use ($dateString) {
                $q->whereDate('exp_date_done', '<', $dateString)->where('done', false);
            });
        })->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->simplePaginate(8)->through(function ($lesson) use ($date, $tz) {
            $dateLesson = Carbon::parse($lesson["exp_date_done"], $tz)->startOfDay();
            // $lastDateLesson = Carbon::parse($lesson["last_exp_date"], $tz)->startOfDay();
            if ($date->greaterThan($dateLesson)) {
                $lesson['f'] = $date->format("Y-m-d H:i:s");
                $lesson['g'] = $dateLesson->format("Y-m-d H:i:s");
                $lesson["late"] = true;
            }
            // if ($lastDateLesson->greaterThan($date)) {
            //     $lesson["new"] = true;
            // }

            $lesson->makeHidden(["date_done", "exp_date_done", "index"]);
            return $lesson;
        });
        return $lessonsForDay;

        // return [
        //     "lessons" => $lessonsForDay,
        //     "course" => [
        //         "end" => $course->finish,
        //     ]
        // ];
    }

    // ->>>>> profile
    public function info(Request $request, $id)
    {

        // $user = $request->user();
        // $course = $this->isExist($user, $id);
        $user = $request->user();
        if ($user) {
            $course = $this->isExist($user, $id);
        } else {
            $profile = $request->profile;
            $course = $this->IsAvailable($profile, $id);
        }

        $tz = $request->timezone;

        $date = now($tz);
        $dateCourseCreate = Carbon::parse($course->created_at)->tz($tz)->startOfDay();

        $result = $this->getWorkDays($course->inSpace, $course->done_days, $dateCourseCreate, $course->weekend, $date);
        return [
            "title" =>  $course->name,
            "data" => [

                "notifs" => $course->notifs,
                "private" => $course->private,
                "finish" => $course->finish,
                "inSpace" => $course->inSpace,
                "lessons" => $course->lessons_number,
                "weekend" => array_map('intval', $course->weekend),
                "days" => $result['doneDays'] === 0 ? 0 : $result['doneDays'] - 1,
                "lessons_limit" => isset($user) ? $user->limits("lessons_limit") : null,
                "note_limit" => isset($user) ? $user->limits("note_limit") : null,
                "days_done_limit" => isset($user) ? $user->limits("days_done_limit") : null,
                "id" => $course->id
            ]
        ];
    }

    public function copy(CopyReq $request, $id)
    {
        $user = $request->user();

        $courseLength = $user->courses()->count();
        if (($courseLength + 1) > $user->limits('courses_limit')) {
            throw ValidationException::withMessages([
                "courses" => "you account have {$user->limits('courses_limit')} course!!"
            ]);
        }

        $course = Course::where("private", false)->find($id) ?? $user->courses()->find($id);

        if (!$course) {
            throw ValidationException::withMessages([
                "course" => 'course not found'
            ]);
        }



        $weekend = $request->weekend ?? [];
        $inSpace = $request->inSpace;
        $doneDays = $request->done_days;
        $lessons = $course->lessons()->select("name")->get()->toArray();

        // return $lessons;

        $user = $request->user();
        $createReq = new CreateReq();
        $createReq->replace([
            "name" => $course->name,
            "lessons" => $lessons,
            "weekend" => $weekend,
            "done_days" => $doneDays,
            "inSpace" => $inSpace
        ]);

        $createReq->setUserResolver(function () use ($user) {
            return $user;
        });

        $resalt = $this->create($createReq, true);


        return ['id' => $resalt->id];
    }
    public function add(AddReq $request, $id, $fromRedist = false)
    {


        $user = $request->user();
        $course = $this->isExist($user, $id);
        $tz = $user->timezone;

        if ($course->finish) {
            return [
                "end" => true
            ];
        }



        $lessons = $request->lessons;
        $lessonsLength = count($lessons);

        if (($course->lessons_number + $lessonsLength) > $user->limits('lessons_limit')) {
            throw ValidationException::withMessages([
                "lessons" => "It is not possible to add more than {$user->limits('lessons_limit')} lessons"
            ]);
        }

        $dateCourseCreate = Carbon::parse($course->created_at)->tz($tz);
        $date = now($tz)->startOfDay();



        $result = $this->getWorkDays($course->inSpace, $course->done_days, $dateCourseCreate, $course->weekend, $date);



        if ($result['workDays'] > 0) {
            // dd($lessons);
            if ($fromRedist) {
                $course->lessons()->select("id", "index", 'exp_date_done', 'done')->whereDate('exp_date_done', ">=", $date->format("Y-m-d"))->get()->each(function ($lesson) {
                    $lesson->update([
                        'last_exp_date' => null
                    ]);
                });
            }
            $courseLessons = $this->redistLessonsDays($course, $date, ['lessons' => $lessons, 'first' => $fromRedist]);
        } else {
            $lessonForDay = intval(ceil(($course->lessons_number / $course->workDays)));
            $numberDaysAdeddNumber = intval(ceil($lessonsLength / $lessonForDay));
            $DaysAgo = $this->getDoneDays($dateCourseCreate, $date, $course);
            $doneDaysFromStart = ($DaysAgo['doneDays'] + $numberDaysAdeddNumber);




            if ($course->inSpace) {
                $dateCalcWorkDays = $dateCourseCreate->startOfDay()->copy()->addDay(($doneDaysFromStart)); // -1
                $workDays = $this->getDoneDays($date, $dateCalcWorkDays, $course)['workDays'];
                while ($workDays < $numberDaysAdeddNumber) {
                    $workDays = $this->getDoneDays($date, $dateCalcWorkDays->addDay(1), $course)['workDays'];
                    $doneDaysFromStart++;
                }
                //  $doneDaysFromStart--;
            }







            if (($doneDaysFromStart) > $user->limits('days_done_limit') && !$fromRedist) {
                for ($i = 0; $i < $lessonsLength; $i++) {
                    if (isset($lessons[$i]["name"])) {
                        $lessons[$i]["name"] = htmlspecialchars($lessons[$i]["name"]);
                    }
                    $lessons[$i]['exp_date_done'] = $course->date_finish;
                }
                $courseLessons = $lessons;
            } else {
                $courseLessons = $this->redistLessonsDays($course, $date, ['lessons' => $lessons, 'first' => $fromRedist], customDoneDays: $doneDaysFromStart);
                $result = $this->getWorkDays($course->inSpace, $doneDaysFromStart, $dateCourseCreate, $course->weekend, $date);


                $course->update([
                    'workDays' => $DaysAgo['workDays'] + $result['workDays'],
                    'done_days' => $doneDaysFromStart,
                    "date_finish" => $date->addDay($result['total'] - 1)
                ]);
            }
        }

        if (!isset($courseLessons)) {
            $courseLessons = [];
        }

        if ($fromRedist) {
            for ($i = 0; $i < count($courseLessons); $i++) {
                $courseLessons[$i]['index'] = $courseLessons[$i]['index'] - count($lessons);
            }
        }





        // update data
        $courseLessons = collect($courseLessons);
        $courseLessons->each(function ($lesson) use ($course) {
            if (isset($lesson["name"])) {
                //!! $lesson["name"] = htmlspecialchars($lesson["name"]);
                $lesson['id'] = null;
            }
            $course->lessons()->updateOrCreate(["id" => $lesson['id']], $lesson);
        });

        if (!$fromRedist) {
            $doneLessons = $course->lessons()->where("done", true)->count();
            $currRatio = round(100 / ($course->lessons_number + $lessonsLength) * $doneLessons, 2);

            $course->update([
                "ratio" => $currRatio
            ]);


            $course->update([
                "lessons_number" => $course->lessons_number + $lessonsLength
            ]);
        }



        return response()->json(["msg" => "Lessons Add Successfully!"]);
    }
    public function extra(Request $request, $id)
    {

        $user = $request->user();
        $course = $this->isExist($user, $id);
        $tz = $request->tz;

        if ($course->finish) {
            return [
                "end" => true
            ];
        }


        $date = Carbon::now($user->timezone);


        // $query = DateConv::CONVERT_TZ("exp_date_done", $user->timezone); //*TZ*
        $dateString = $date->format("Y-m-d");


        ////*TZ* $isHaveLessonsNotDone = $course->lessons()->select("*")->selectRaw("$query as exp_date_done")->whereRaw($query . "<= '$dateString' AND done = 0")->exists();
        $isHaveLessonsNotDone = $course->lessons()->whereDate('exp_date_done', "<=", $dateString)->where('done', false)->exists();

        if ($isHaveLessonsNotDone) {
            return response()->json(["msg" => "You cannot add lessons for today, you have unfinished lessons"]);
        };


        //*TZ* $lessonAddedToDay = $course->lessons()->select("*")->selectRaw("$query as exp_date_done")->whereRaw($query . "> '$dateString'")->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get()->first();

        $lessonAddedToDay = $course->lessons()->whereDate('exp_date_done', ">", $dateString)->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get()->first();


        if (!$lessonAddedToDay) {
            return response()->json(["msg" => "You do not have extra lessons"]);
        }

        if (is_null($lessonAddedToDay->last_exp_date) || Carbon::parse($lessonAddedToDay->exp_date_done, $tz)->startOfDay()->greaterThan(Carbon::parse($lessonAddedToDay->last_exp_date, $tz)->startOfDay())) {
            $lessonAddedToDay->update([
                "last_exp_date" => $lessonAddedToDay->exp_date_done,
                "exp_date_done" => $date //*TZ* ->utc()
            ]);
        } else {
            $lessonAddedToDay->update([
                // "last_exp_date" => $lessonAddedToDay->exp_date_done,
                "exp_date_done" => $date //*TZ* ->utc()
            ]);
        }

        // ->where('done',false)
        $FutureLessons = $course->lessons()->whereDate('exp_date_done', ">", $dateString)->where(function ($q) {
            $q->whereRaw('DATE(last_exp_date) < DATE(exp_date_done)')->orWhere('last_exp_date', null);
        })->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get();

        $FutureLessons->each(function ($lesson) use ($tz) {
            $lesson->update([
                "last_exp_date" => $lesson->exp_date_done,
            ]);
        });
        //!!!!!!!!TEST!!!!!!!

        // Redistributing
        //->where('done', false)
        // customLessons: $course->lessons()->whereNotIn('id', [$lessonAddedToDay->id])->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get()->toArray()
        $courseLessons = $this->redistLessonsDays($course, $date->addDay(1));

        $courseLessons = collect($courseLessons);
        $courseLessons->each(function ($lesson) use ($course) {
            $course->lessons()->updateOrCreate(["id" => $lesson['id']], $lesson);
        });

        //  dump($courseLessons->toArray());
        //  $course->update([
        //      "date_finish" => $courseLessons->last()["exp_date_done"] ?? $date->utc()
        //  ]);

        // Update

        // $dateLesson = Carbon::parse($lessonAddedToDay->exp_date_done, $tz) //*TZ*;

        $lessonAddedToDay->makeHidden(["date_done", "exp_date_done", "index"]);

        return response()->json($lessonAddedToDay);
    }

    public function redist(Request $request, $id)
    {
        $user = $request->user();
        $course = $this->isExist($user, $id);
        $tz = $request->tz;
        if ($course->finish) {
            return [
                "end" => true
            ];
        };

        $date = Carbon::now($tz);

        // dd($date->format("Y-m-d"));


        $dateString = $date->format("Y-m-d");


        $lateLessons = collect($course->lessons()->select("id", "index", 'exp_date_done', 'done')->where("done", false)->whereDate("exp_date_done", "<", $dateString)->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get());



        // $stayLessons = collect($course->lessons()->whereDate("exp_date_done", ">=", $dateString)->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get());


        if (($lateLessons->count() > 0)) {
            $addReq = new AddReq();
            $addReq->replace([
                'lessons' => $lateLessons->toArray()
            ]);

            $addReq->setUserResolver(function () use ($user) {
                return $user;
            });

            $this->add($addReq, $course->id, fromRedist: true);

            $oldLessonsDone = $course->lessons()->select("id", "index", 'exp_date_done', 'done')->where("done", true)->whereDate("exp_date_done", "<", $dateString)->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get();

            $oldLessonsDone->each(function ($lesson, $index) {
                $lesson->update([
                    'index' => $index + 1
                ]);
            });
            // $dateCourseCreate = Carbon::parse($course->created_at)->tz($tz);
            // $result = $this->getWorkDays($course->inSpace, $course->done_days, $dateCourseCreate, $course->weekend, $date);

            // if($result['workDays'] > 0) {
            //     // course
            //     // $allLessons = $lateLessons->merge($stayLessons)->toArray();
            //     $newLessons = collect($this->redistLessonsDays($course, $date, ['lessons'=> $lateLessons->toArray()]));

            //     dd();
            // } else {
            //     // End course
            // }
            // dd();
            // $remainingLessons = $course->lessons()->whereDate('')->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get();

            // $countDone = 1;
            // $countNotDone = $todayAndLessLessons - $lateLessons + 1;
            // $remainingLessons = $remainingLessons->map(function ($lesson) use (&$countDone, &$countNotDone) {
            //     if ($lesson->done) {
            //         $lesson->update([
            //             "index" => $countDone
            //         ]);
            //         $countDone++;
            //     } else {
            //         $lesson->index = $countNotDone;
            //         $countNotDone++;
            //     }
            //     return $lesson;
            // })->reject(function ($lesson) {
            //     return $lesson->done;
            // })->values()->toArray();



            // $newLessons = collect($this->redistLessonsDays($course, $date, customLessons: $remainingLessons));


            // //update !!!!!!!!!!!!!!!
            // $course->update([
            //     "date_finish" => $newLessons->last()['exp_date_done']
            // ]);

            // $newLessons->each(function ($lesson) use ($course) {
            //     $course->lessons()->updateOrCreate(["id" => $lesson['id']], $lesson);
            // });

            // dd($newLessons);
        }


        return response()->json(["msg" => "Late Lessons Reset Successfully!!"]);
    }

    public function update(UpdateReq $request)
    {
        $user = $request->user();
        // $course = $this->isExist($user, $id);
        $tz = $request->tz;
        $course = null;
        $lessons = collect($request->lessons);



        $lessons->each(function ($lesson) use ($user, $tz, $course) {
            // Chick Exist
            $lessonDB = $user->lessons()->find($lesson['id']);
            if (!$lessonDB) {
                return;
            }

            if (!$course) {
                $course = $lessonDB->course;
                if ($course->finish) {
                    return false;
                };
            } else if ($course->id !== $lessonDB->course_id) {
                return;
            }

            $updated_at = $lessonDB->updated_at;

            $canUpdateValue = function ($dateChanged) use ($tz, $updated_at) {
                $dateLessonRename = Carbon::parse($updated_at)->tz($tz);
                $dateChanged = Carbon::parse($dateChanged)->tz($tz);
                return $dateChanged->greaterThan($dateLessonRename);
            };



            $updateData = [];


            if (isset($lesson["process"]["name"])) {
                if ($canUpdateValue($lesson["process"]["name"])) {
                    $updateData['name'] = htmlspecialchars($lesson["name"]);
                }
            }


            if (isset($lesson["process"]["note"])) {
                if ($canUpdateValue($lesson["process"]["note"])) {
                    $notePufter = clean($lesson["note"]);
                    if (strlen($notePufter) <= 25000) {
                        $updateData['note'] = $notePufter;
                    }
                }
            }

            if (isset($lesson["process"]["set"]) && !$course->finish) {
                $dateExpDoneLesson = Carbon::parse($lessonDB->exp_date_done, $tz)->startOfDay(); //*TZ* ->tz($tz)
                $dateDone = Carbon::parse($lesson["process"]["set"])->tz($tz)->startOfDay();
                $date = Carbon::now($tz)->startOfDay();

                if (($dateDone->equalTo($dateExpDoneLesson) || (!$lessonDB->done && $dateDone->greaterThan($dateExpDoneLesson))) && $dateDone->lessThanOrEqualTo($date)) {
                    $updateData["done"] = $lesson["done"];
                }
            }

            if (!empty($updateData)) {
                $lessonDB->update($updateData);
            }
        });




        return response()->json(["msg" => "Offline Changes Updated Successfully!!"]);
    }

    public function image(Request $request, $id)
    {

        $user =  Auth::user();
        // dd($user);
        $course = Course::where("private", false)->find($id);

        if (!$course && $user) {
            $course = $user->courses()->find($id);
        }



        if (!$course || !Storage::disk('courses')->exists($course->id . '.jpg')) {
            abort(404, 'Image Not Found');
        }



        return response(Storage::disk('courses')->get($course->id . '.jpg'))->header('Content-Type', 'image/jpeg');



        // $course = $this->IsAvailable($profile, $id);

        // if ($user) {
        //     $course = $this->isExist($user, $id);
        // } else {
        //     $profile = $request->profile;

        // }


    }

    public function upload(UploadReq $request, $id)
    {

        $user = $request->user();
        $course = $this->isExist($user, $id);
        $image = $request->image;
        $imageSize = $image->getSize() / 1024 / 1024;

        $x = $request->x;
        $y = $request->y;
        $width = $request->width;
        $height = $request->height;


        $imageStore = ImageUploader::proces($image, [$x, $y, $width, $height], [230, 125], 1.84, 2);


        Storage::disk('courses')->put($id . '.jpg', $imageStore);

        return ['image' => 'image Upload Done'];
    }
    // =============== Helpers ===============

    // Redistributing lessons across days + Add Lessons To Course
    public function redistLessonsDays(Course $course, Carbon $fromDate, array $AddLessons = ['lessons' => [], 'afterID' => null, 'first' => false], array $customLessons = null, $customWeekend = null, $customDoneDays = null) : array // ,array $customLessons = []
    {
        $user = $course->user;
        $tz = $user->timezone;
        $weekend = $customWeekend ?? $course->weekend ?? [];
        $inSpace = $course->inSpace;

        $date = now($user->timezone);

        $dateCourseCreate = Carbon::parse($course->created_at)->tz($user->timezone)->startOfDay();


        $doneDays = $customDoneDays ?? $course->done_days;


        $result = $this->getWorkDays($inSpace, $doneDays, $dateCourseCreate, $weekend, $fromDate);

        if ($result['workDays'] <= 0) {
            throw ValidationException::withMessages([
                "workDays" => "You Dont Have Any Work Days"
            ]);
        }

        $dateString = $fromDate->format("Y-m-d");

        // GET Lessons $fromDate to End Course
        $courseLessons = $customLessons ?? collect($course->lessons()->select("id", "index", 'exp_date_done', 'done')->whereDate('exp_date_done', ">=", $dateString)
            ->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get()->toArray());

        // $date
        $lessonsDoneToday = $courseLessons->where(function ($value) use ($tz, $date) {
            return Carbon::parse($value['exp_date_done'], $tz)->isSameDay($date) && $value['done'];
        });





        if (isset($AddLessons['afterID'])) {

            $indexPush = null;

            $courseLessons->transform(function ($lesson, $index) use ($AddLessons, &$indexPush) {
                if (isset($indexPush)) {
                    $lesson["index"] = $lesson["index"] + count($AddLessons['lessons']);
                }

                if ($lesson['id'] === $AddLessons['afterID']) {
                    $indexPush = $index;
                }
                return $lesson;
            });

            $courseLessons->splice($indexPush + 1, 0, $AddLessons['lessons']);

            // $courseLessons = $courseLessons;
        } elseif (isset($AddLessons['first']) && $AddLessons['first']) {
            // $course->lessons()->where();
            // $lastIndex = $course->lessons()->select("index")->whereDate('exp_date_done', "<", $dateString)
            // ->orderBy("index", "desc")->limit(1)->first();
            if ($courseLessons->count()) {
                $lastIndex = $courseLessons[0]['index'];
            } else {
                $lastIndex = $course->lessons_number + 1;
            }
            // dd($lastIndex);

            // dd($lastIndex->index);

            // if($lastIndex) {
            //     $lastIndex = $lastIndex->index;
            // } else {
            //     $lastIndex = 1;
            // }

            $AddLessons['lessons'] = collect($AddLessons['lessons']);

            $courseLessons->transform(function ($lesson, $index) use ($AddLessons) {
                // if($index < count($AddLessons['lessons'])) {
                //     $AddLessons['lessons'][$index]['index'] = $lesson['index'];
                // }

                $lesson['index'] = $lesson['index'] + $AddLessons['lessons']->count();
                return $lesson;
            });

            //  dd($lastIndex);

            $AddLessons['lessons']->transform(function ($lesson, $index) use ($lastIndex) {
                $lesson['index'] = $index + $lastIndex;
                return $lesson;
            });

            $courseLessons =  $AddLessons['lessons']->merge($courseLessons);

            // dd($courseLessons);
        } else {
            $courseLessons = $courseLessons->merge($AddLessons['lessons']);
        }




        $courseLessons = $courseLessons->toArray();

        // dd($fromDate->format("Y-m-d H:i:s"));
        // set
        $this->setLessonsDays($courseLessons, $result['workDays'], $weekend, $inSpace, $fromDate);

        // dd('here');

        if ($lessonsDoneToday->isNotEmpty()) {

            $courseLessons = collect($courseLessons);

            $lessonsDoneChanged = $lessonsDoneToday->where(function ($value) use ($courseLessons, $tz) {
                return $courseLessons->where(function ($valueCourse) use ($tz, $value) {
                    return ($valueCourse['id'] == $value['id']) && (!Carbon::parse($value['exp_date_done'], $tz)->isSameDay(Carbon::parse($valueCourse['exp_date_done'], $tz)));
                })->first();
            });

            if ($lessonsDoneChanged->isNotEmpty()) {
                $todayLessons = $courseLessons->filter(function ($value) use ($tz, $date) {
                    return Carbon::parse($value['exp_date_done'], $tz)->isSameDay($date);
                });

                $otherLessons = $courseLessons->filter(function ($value) use ($tz, $date) {
                    return Carbon::parse($value['exp_date_done'], $tz)->greaterThan($date);
                });



                $numLesChgd = $lessonsDoneChanged->count();


                // from today to tomoro
                $indexTodayLessons = $todayLessons->count();
                $pushToOtherLessons = collect([]);

                //
                $todayLessons = $todayLessons->filter(function ($item) use (&$indexTodayLessons, $numLesChgd, &$pushToOtherLessons) {
                    if (!$item['done'] && ($indexTodayLessons <= $numLesChgd)) {
                        $pushToOtherLessons->push($item);
                        return false;
                    }
                    $indexTodayLessons--;
                    return true;
                });
                // dd($pushToOtherLessons);
                $otherLessons = $pushToOtherLessons->merge($otherLessons);


                // from tomoro to today
                $dateFlep = collect([]);
                $otherLessons =  $otherLessons->filter(function ($item) use (&$todayLessons, &$lessonsDoneToday, &$indexOtherLessons, &$dateFlep) {
                    if ($item['done']) {
                        $dateFlep->push($item['exp_date_done']);
                        $item['exp_date_done'] = $lessonsDoneToday->first()['exp_date_done'];
                        $todayLessons->push($item);
                        return false;
                    }
                    return true;
                });


                // reindex
                $index =  $courseLessons->first()['index'];
                $todayLessons->transform(function ($item) use ($lessonsDoneToday, &$index) {
                    $item['index'] = $index;
                    $index++;
                    return $item;
                });

                $otherLessons = $otherLessons->map(function ($item, $key) use (&$index) {
                    $item['index'] = $index;
                    $index++;
                    return $item;
                })->values()->toArray();


                $numberLessonsTodayMoveToTomooro = $pushToOtherLessons->count();
                if ($numberLessonsTodayMoveToTomooro < $numLesChgd) {
                    $this->setLessonsDays($otherLessons, $result['workDays'], $weekend, $inSpace, $fromDate->addDay(1));
                } else {
                    for ($i = 0; $i < $dateFlep->count(); $i++) {
                        $otherLessons[$i]['exp_date_done'] = $dateFlep[$i];
                    };
                }





                $fullLessonsCourse = $todayLessons->merge($otherLessons)->toArray();
                // dd($fullLessonsCourse);

                return $fullLessonsCourse;
            } else {
                $courseLessons = $courseLessons->toArray();
            }
        }

        // dump($courseLessons);

        // dd($courseLessons);

        // AFTER Rearranging

        return $courseLessons;
    }
    // Assign lessons to days
    private function setLessonsDays(&$lessons, $workDays, $weekend, $inSpace, $date)
    {

        $lessonsLength = count($lessons);
        if ($lessonsLength === 0) {
            return;
        }


        $lessonForDay = intval($lessonsLength / $workDays);
        $singleLessons = $lessonsLength % $workDays;
        $isLessonsSmaller = $lessonsLength < $workDays;
        $spaces =  $singleLessons != 0 ? $workDays - $singleLessons : 0;
        $works = $workDays - $spaces;


        // create single lessons if have
        $singleLessonsDist = [];
        if ($singleLessons) {
            if ($isLessonsSmaller) {
                // work
                $singleLessonsDist = $this->linespace(1, $workDays, $works, true);
            } else {
                if ($spaces > 1) {

                    // space
                    $singleLessonsDist = $this->linespace(1,  $workDays, $spaces, true, true);
                } else {

                    // work
                    $singleLessonsDist = $this->linespace(1, $workDays, $works, true);
                    // var_dump($singleLessonsDist);
                }
            }
        }





        $setLesson = 1;
        $todaySet = $lessonForDay;
        $dateToCalcDoubleLessons = $date->copy();
        $day = 1;



        // part to add day single days to set days
        $minsDay = 0;
        $isAddedSmaller = false;
        if ($isLessonsSmaller) {
            if ($singleLessonsDist[0] !== 1) {
                $dateToCalcDoubleLessons->addDay($singleLessonsDist[0] - 1);
                $minsDay += $singleLessonsDist[0] - 1;
                array_shift($singleLessonsDist);
                $isAddedSmaller = true;
            } else {
                array_shift($singleLessonsDist);
            }
        } else {
            // add single liseeons
            // if day 1 have single lessons
            if (in_array($day, $singleLessonsDist)) {
                $singleLessonsForDay = array_count_values($singleLessonsDist)[$day];
                $singleLessonsDist = array_filter($singleLessonsDist, fn ($single) => $single !== $day);
                $todaySet = $lessonForDay + $singleLessonsForDay;
            } else {
                $todaySet = $lessonForDay;
            }
        }


        // chick How Many Have Lessons today becase lessons add to this day
        /*
        I modified this part so that when some lessons the user has added lessons from tomorrow, then today’s lessons will be more than the divided lessons. Instead of this code, it will return the lesson that was added for today to its place.
        */


        $todayHowManyLessons = count(array_filter($lessons, function ($lesson) use ($dateToCalcDoubleLessons) {
            if (isset($lesson['exp_date_done'])) {
                $expDate = $lesson['exp_date_done'];
                return $expDate === $dateToCalcDoubleLessons->format("Y-m-d");
            }
            return false;
        }));

        if ((($todayHowManyLessons > $todaySet) && !$isLessonsSmaller) || ($isLessonsSmaller && ($todayHowManyLessons > 1))) {
            $lessons = array_values(array_filter($lessons, function ($lesson) use ($dateToCalcDoubleLessons) {

                if (isset($lesson['exp_date_done'])) {
                    $expDate = $lesson['exp_date_done'];
                    $todayDate = $dateToCalcDoubleLessons->format("Y-m-d");

                    return $expDate !== $todayDate;
                }
                return true;
            }));


            if (!$isLessonsSmaller || !$isAddedSmaller) {
                $dateToCalcDoubleLessons->addDay(1);
            }

            return $this->setLessonsDays($lessons, $workDays - 1, $weekend, $inSpace, $dateToCalcDoubleLessons);
        }



        for ($i = 0; $i < $lessonsLength; $i++) {
            //Skip WeekEnd
            if ((in_array($dateToCalcDoubleLessons->dayOfWeek, $weekend))) {
                $dateToCalcDoubleLessons->addDay(1);
                $i--;
                continue;
            }

            if (isset($lessons[$i]['name'])) {
                $lessons[$i]['name'] = htmlspecialchars($lessons[$i]['name']);
            }

            $lessons[$i]['exp_date_done'] = $dateToCalcDoubleLessons->format("Y-m-d H:i:s"); //*TZ* ->copy()->utc()


            if ($isLessonsSmaller) {
                if (!empty($singleLessonsDist)) {
                    $dateToCalcDoubleLessons->addDay($singleLessonsDist[0] - $minsDay - 1);
                    $minsDay += $singleLessonsDist[0] - $minsDay  - 1;
                    array_shift($singleLessonsDist);
                }
            } else {
                if ($setLesson == $todaySet) {
                    $dateToCalcDoubleLessons->addDay(1);

                    $setLesson = 0;
                    $singleLessonsDist = array_filter($singleLessonsDist, fn ($single) => $single !== $day);
                    $day++;

                    if (in_array($day, $singleLessonsDist)) {
                        $nextDaySetLessons = array_count_values($singleLessonsDist)[$day];
                        $todaySet = $lessonForDay + $nextDaySetLessons;
                    } else {
                        $todaySet = $lessonForDay;
                    }
                }

                $setLesson++;
            }
        }
    }
    // function for [create()]
    private function linespace($start, $end, $num, $isRound = false, $reverse = false)
    {
        if ($num == 1) {
            return [$end];
        }

        $array = [];

        $step = ($end - 1) / ($num - 1);
        for ($i = $start; $i < $end + 1; $i = $i + $step) {
            array_push($array, $isRound ? intval(round($i)) : $i);
        }

        // [1,1,1,3];
        // [2]

        if ($reverse && $isRound) {
            $reverseArray = [];
            for ($i = 1; $i < $end + 1; $i++) {
                if (!in_array($i, $array)) {
                    array_push($reverseArray, $i);
                }
            }

            return $reverseArray;
        }

        return $array;
    }

    public function getWorkDays($inSpace, $doneDays, $dateStart, $weekend = [], $remaFromDate = null)
    {
        // if (!$inSpace && !isset($remaFromDate) && !$calcDoneDays) {
        //     return $doneDays;
        // }


        $dateStart = $dateStart->copy()->startOfDay();
        $daysAgoWithotWeekend = 0;
        $daysAgo = 0;
        $workDays = 0;
        $weekendsDays = 0;
        $totalDays = 0;
        for ($i = 0; $i < $doneDays; $i++) {
            $isWeekend = in_array($dateStart->dayOfWeek, $weekend);
            $isTodayOrPlus = isset($remaFromDate) ? (($remaFromDate->copy()->startOfDay()->diffInDays($dateStart, false)) >= 0) : true;

            if (!$isTodayOrPlus) {
                $daysAgo++;
            }


            $dateStart->addDay(1);


            if (($isWeekend && $inSpace) || (!$isWeekend)) {
                $workDays++;
            }

            if (($isWeekend && $inSpace && (!$isTodayOrPlus)) || ((!$isTodayOrPlus) && !$isWeekend)) {
                $daysAgoWithotWeekend++;
            }

            if ($isWeekend && $inSpace && $isTodayOrPlus) {
                $weekendsDays++; // if weekend space
            }

            $totalDays++;

            // dump($weekendsDays == $totalDays);

            if (($isWeekend && !$inSpace)) { //  || (($weekendsDays == $totalDays) && ($i === $doneDays - 1) && $inSpace)
                $i--;
                continue;
            }
        }

        //
        // dd($totalDays);
        return ['doneDays' => $doneDays - $daysAgoWithotWeekend, 'workDays' => ($workDays - $daysAgoWithotWeekend - $weekendsDays), 'total' => $totalDays - $daysAgo];



        // return ($workDays - $daysAgo - $weekendsDays);
    }

    private function getDoneDays(Carbon $startDate, Carbon $endDate, $course = null, $weekend = null, $inSpace = null)
    {

        $weekend = isset($course->weekend) ? $course->weekend : $weekend;
        $inSpace = isset($course->inSpace) ? $course->inSpace : $inSpace;
        $startDate = $startDate->copy()->startOfDay();
        $endDate = $endDate->copy()->startOfDay();
        $workDays = 0;
        $doneDays = 0;
        $totalDays = 0;

        while (true) {
            if ($startDate->equalTo($endDate)) {
                break;
            }
            $isWeekend = in_array($startDate->dayOfWeek, $weekend);

            if ((!$isWeekend)) {
                $workDays++;
            }

            if (($isWeekend && $inSpace) || (!$isWeekend)) {
                $doneDays++;
            }

            $totalDays++;

            $startDate->addDay(1);
        }


        return ['doneDays' => $doneDays, 'workDays' => $workDays, 'total' => $totalDays];
    }



    // Chick if User Have This Course, if valid return
    private function isExist(User $user, $id): Course
    {
        $course =  $user->courses()->find($id);
        if (!$course) {
            throw ValidationException::withMessages([
                "course" => 'course not found'
            ]);
        }

        $course->activate();
        return $course;
    }
    private function isAvailable($profile, $id)
    {
        $course = $profile->courses()->where("private", false)->find($id);
        if (!$course) {
            throw ValidationException::withMessages([
                "course" => 'course not found'
            ]);
        }

        return $course;
    }
}
