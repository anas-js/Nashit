<?php

namespace App\Http\Controllers;

use App\Helpers\DateConv;
use App\Http\Requests\CoursesController\AddReq;
use App\Http\Requests\LessonsController\moveReq;
use App\Http\Requests\LessonsController\noteReq;
use App\Http\Requests\LessonsController\renameReq;
use App\Http\Requests\LessonsController\setReq;
use App\Models\Course\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LessonsController extends Controller
{
    // =============== Pages ===============
    public function rename(renameReq $request, $id)
    {
        $user = $request->user();
        $lesson = $this->isExist($user, $id);
        $newName = $request->name;

        $lesson->update([
            "name" => htmlspecialchars($newName)
        ]);

        return ["msg" => "Lesson Renamed Successfully!!"];
    }

    public function set(setReq $request, $id)
    {
        $user = $request->user();
        $lesson = $this->isExist($user, $id);
        $course = $lesson->course;
        $tz = $request->tz;

        if ($course->finish) {
            throw ValidationException::withMessages([
                "course" => "course finish!"
            ]);
        }

        $date = Carbon::now($tz)->startOfDay();

        $lesson_expDate = Carbon::parse($lesson->exp_date_done, $tz)->startOfDay(); //*TZ* ->tz($tz)
        $lesson_done = $lesson->done;


        if ($date->equalTo($lesson_expDate) || (!$lesson_done && $date->greaterThan($lesson_expDate))) {
            $set = $request->set;
            if ($set !== $lesson_done) {
                $lesson->update([
                    "done" => $set,
                    "date_done" => $set ? now($tz) : null
                ]);
            }
        } else if ($date->notEqualTo($lesson_expDate)) {
            throw ValidationException::withMessages([
                "lesson" => "cant Change Status Of Lesson"
            ]);
        }

        $doneLessons = $course->lessons()->where("done", true)->count();
        $currRatio = round(100 / $course->lessons_number * $doneLessons, 2);

        $lessons_rema = ($course->lessons_number - $doneLessons);

        $course->update([
            "ratio" => $currRatio,
            'finish' => $lessons_rema === 0,
            'date_done' => $lessons_rema === 0 ? now($tz) : null
        ]);

        if ($lessons_rema === 0) {
            return [
                "end" => true
            ];
        } else if ($lessons_rema === 1) {
            return [
                "last" => true
            ];
        }

        return ["msg" => "Lesson Status Changed Successfully!!"];
    }

    public function note(noteReq $request, $id)
    {
        $user = $request->user();
        $lesson = $this->isExist($user, $id);
        $note = $request->note;

        $notePufter = clean($note) ?: null;

        if (strlen($notePufter) > 25000) {
            throw ValidationException::withMessages([
                "lesson" => "The note field must not be greater than 25000 Byte"
            ]);
        }

        $lesson->update([
            "note" => $notePufter
        ]);

        if ($request->returnSmallNote) {
            return ["smallNote" => mb_substr(strip_tags($lesson["note"]), 0, 100)];
        }


        return ["msg" => "Lesson Notebook Changed Successfully!!"];
    }

    public function remove(Request $request, $id)
    {
        $user = $request->user();
        $lesson = $this->isExist($user, $id);
        $course = $lesson->course;
        $tz = $request->tz;

        if ($course->lessons_number === 1) {
            throw ValidationException::withMessages([
                "lesson" => "Cant Remove Last Lesson"
            ]);
        }

        $lesson->delete();

        $date = Carbon::now($tz);
        $lesson_expDate = Carbon::parse($lesson->exp_date_done, $tz)->startOfDay(); //*TZ* ->tz($tz)

        if ($lesson_expDate->greaterThanOrEqualTo($date->copy()->startOfDay()) && !$course->finish) {
            $lessonsRedist = collect(app(CoursesController::class)->redistLessonsDays($course, $date));

            $lessonsRedist->each(function ($lesson) use ($course) {
                $course->lessons()->updateOrCreate(["id" => $lesson['id']], $lesson);
            });
        }

        $doneLessons = $course->lessons()->where("done", true)->count();


        if ($course->finish) {
            $course->update([
                "lessons_number" => $course->lessons_number - 1
            ]);
        } else {
            $currRatio = round(100 / ($course->lessons_number - 1) * $doneLessons, 2);
            $course->update([
                "ratio" => $currRatio,
                'finish' => (($course->lessons_number - 1) - $doneLessons) === 0,
                'date_done' => ((($course->lessons_number - 1) - $doneLessons) === 0) ? now($tz) : null,
                "lessons_number" => $course->lessons_number - 1
            ]);
        }




        return ["msg" => "Lesson Removed Successfully!!"];
    }

    public function move(moveReq $request, $id)
    {
        $user = $request->user();
        $lesson = $this->isExist($user, $id);
        $course = $lesson->course;
        $tz = $request->tz;
        $after = $request->after;
        $before = $request->before;


        if ($course->finish) {
            throw ValidationException::withMessages([
                "course" => "course finish!"
            ]);
        }

        $lessonAfterOrBefore = null;
        $position = null;
        $date = Carbon::now($tz)->startOfDay();


        if (isset($after)) {
            $lessonAfterOrBefore = $this->isExist($user, $after);
            $position = 0;

            if (($lessonAfterOrBefore->index + 1) === $lesson->index) {
                // var_dump("after Arrayedy");
                return ["msg" => "Lesson Moved Successfully!"];
            }


            if ($lesson->done && Carbon::parse($lessonAfterOrBefore->exp_date_done, $tz)->startOfDay()->greaterThan($date)) {
                throw new HttpResponseException(response()->json([
                    'lesson' => 'The completed lesson cannot be transferred to tomorrow.',
                    'code' => 2
                ],404));
            }

            // $lessonAfterOrBefore->exp_date_done,$tz

            // dd($lessons->toArray());
            // $afterDate = Carbon::parse();

            // if($lesson->done && ) {

            // }
        } else {
            $lessonAfterOrBefore = $this->isExist($user, $before);
            $position = 1;

            if (($lessonAfterOrBefore->index - 1) === $lesson->index) {
                // var_dump("before Arrayedy");
                return ["msg" => "Lesson Moved Successfully!"];
            }



            if ($lesson->done) {
                $lessons = collect($course->lessons()->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get());
                $lessons->each(function ($lessonFind, $key) use ($lessonAfterOrBefore, $lessons, $tz, $date) {
                    if (($lessonAfterOrBefore->id === $lessonFind->id)) {

                        // dd($lessons[isset($after) ? $key - 1 : $key + 1 ]->toArray());
                        if (isset($lessons[$key - 1]) && Carbon::parse($lessons[$key - 1]->exp_date_done, $tz)->startOfDay()->greaterThan($date)) {
                            throw new HttpResponseException(response()->json([
                                'lesson' => 'The completed lesson cannot be transferred to tomorrow.',
                                'code' => 2
                            ],404));
                        } else if (($key == 0) &&Carbon::parse($lessonFind->exp_date_done, $tz)->startOfDay()->greaterThan($date)) {
                            throw new HttpResponseException(response()->json([
                                'lesson' => 'The completed lesson cannot be transferred to tomorrow.',
                                'code' => 2
                            ],404));
                        }
                    }
                });
            }

        }
        // dd('d');


        if ($lessonAfterOrBefore->id === $lesson->id) {
            throw ValidationException::withMessages([
                "lesson" => 'Lesson is Same'
            ]);
        }









        $lessonsAfterChange =  $this->changePosition($course, $lesson, $lessonAfterOrBefore, $position, $tz);

        if ($lessonsAfterChange) {
            collect($lessonsAfterChange)->each(function ($lesson) use ($course) {
                $course->lessons()->updateOrCreate(["id" => $lesson['id']], $lesson);
            });
        }

        return ["msg" => "Lesson Moved Successfully!"];
    }

    public function copy(Request $request, $id)
    {
        $user = $request->user();
        $lesson = $this->isExist($user, $id);
        $course = $lesson->course;
        $tz = $request->tz;

        if ($course->finish) {
            throw ValidationException::withMessages([
                "course" => "course finish!"
            ]);
        }
        if (($course->lessons_number + 1) > $user->limits('lessons_limit')) {
            throw ValidationException::withMessages([
                "lessons" => "It is not possible to add more than {$user->limits('lessons_limit')} lessons"
            ]);
        }
        $dateCourseCreate = Carbon::parse($course->created_at)->tz($user->timezone)->startOfDay();


        $date = Carbon::now($tz)->startOfDay();

        // $date->addDay(13);


        $result = app(CoursesController::class)->getWorkDays($course->inSpace, $course->done_days, $dateCourseCreate, $course->weekend, $date);
        $lessonDateExp = Carbon::parse($lesson->exp_date_done)->tz($tz);
        $copy = $lesson->only("index", "name", "note");
        $copy["id"] = null;


        if ($result['workDays'] > 0) {
            if ($lessonDateExp->copy()->startOfDay()->greaterThanOrEqualTo($date)) {
                // "exp_date_done" "done" "date_done"
                $copy["index"] = $copy["index"] + 1;
                $newLessons = collect(app(CoursesController::class)->redistLessonsDays($course, $date, ['lessons' => [$copy], 'afterID' => $lesson->id]));
            } else {
                $newLessons = collect(app(CoursesController::class)->redistLessonsDays($course, $date, ['lessons' => [$copy], 'first' => true]));
            }


            $newLessons->each(function ($lesson) use ($course) {
                $course->lessons()->updateOrCreate(["id" => $lesson['id']], $lesson);
            });
        } else {
            unset($copy["index"]);
            $addReq = new AddReq();
            $addReq->replace([
                'lessons' => [$copy]
            ]);

            $addReq->setUserResolver(function () use ($user) {
                return $user;
            });

            app(CoursesController::class)->add($addReq, $course->id);
        }






        // if ($lessonDateExp->copy()->startOfDay()->greaterThanOrEqualTo($date)) {
        //     $copy = $lesson->only("index", "name", "done", "note", "date_done"); // "exp_date_done"
        //     $copy["id"] = null;
        //     $copy["index"] = $copy["index"] + 1;

        //     $newLessons = collect(app(CoursesController::class)->redistLessonsDays($course, $date, ['lessons'=> [$copy],'afterID'=> $lesson->id]));



        //     $newLessons->each(function ($lesson) use ($course) {
        //         $course->lessons()->updateOrCreate(["id" => $lesson['id']], $lesson);
        //     });

        //     // redist
        // } else {
        //     // copy to lessons to day if exist
        //     $copy = $lesson->only("index", "name", "done", "note", "date_done", "exp_date_done");
        //     $copy["index"] = $lesson->index + 1;
        //     $lessonsIndex = $course->lessons()->where("index", ">", $lesson->index)->get();
        //     dd('test');
        //     for ($i = 0; $i < $lessonsIndex->count(); $i++) {
        //         $lessonsIndex[$i]->update([
        //             "index" => $lessonsIndex[$i]["index"] + 1
        //         ]);
        //     }
        //     $course->lessons()->create($copy);


        // }

        $doneLessons = $course->lessons()->where("done", true)->count();
        $currRatio = round(100 / ($course->lessons_number + 1) * $doneLessons, 2);

        $course->update([
            "ratio" => $currRatio,
            "lessons_number" => $course->lessons_number + 1
        ]);

        // $lesson = $course->lessons()->where('index', $lesson->index + 1)->first();
        // //  return ['index'=> $course->lessons()->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->where('index')];

        // $exp_date_done = $lesson->exp_date_done;


        // $lesson = collect($lesson);
        // $lesson["date"] = collect([
        //     "required" => Carbon::parse($lesson['exp_date_done'])->tz($tz)->format("Y-m-d"),
        //     "done" => null
        // ]);

        // // if ($lesson['date_done']) {
        // //     $lesson["date"]['done'] = Carbon::parse($lesson['date_done'])->tz($tz)->format("Y-m-d");
        // // }

        // $lesson["smallNote"] = mb_substr(strip_tags($lesson["note"]), 0, 100);


        // return [
        //     "index" => $course->lessons()->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->whereRaw("DATE(exp_date_done) < DATE('$exp_date_done')")->count(),
        //     'lesson' => $lesson->except("course_id", "date_done", "exp_date_done", "index")
        // ];
        // ->search(function ($item, $key) use ($lesson) {
        //     return $item === $lesson->id;
        // });

        // $lesson->index+1



        return ["msg" => "Lesson Copied Successfully!"];
    }

    // =============== Helpers ===============

    // Chick if User Have This Lesson, if valid return
    private function isExist(User $user, $id): Lesson
    {
        $lesson =  $user->lessons()->find($id);
        if (!$lesson) {
            throw ValidationException::withMessages([
                "lesson" => 'lesson not found'
            ]);
        }

        $lesson->course->activate();
        return $lesson;
    }
    private function changePosition($course, $lesson, $pointerLesson, $position, $tz)
    {
        $date = $this->getOldestDate($lesson->exp_date_done, $pointerLesson->exp_date_done, $tz);

        $dateString = $date->format("Y-m-d");

        //*TZ* $query = DateConv::CONVERT_TZ("exp_date_done", $tz, true);
        //*TZ* $courseLessons = $course->lessons()->select("id", "index")->selectRaw("$query as exp_date_done")->whereRaw($query . ">= '$dateString'")->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get()->toArray();

        $courseLessons = $course->lessons()->select("id", "index", 'exp_date_done', 'last_exp_date')->whereDate("exp_date_done", ">=", $dateString)->orderByRaw("DATE(exp_date_done) ASC, `index` ASC")->get()->makeVisible(['last_exp_date'])->toArray();
        // dd($courseLessons);

        $lastCourseLessons = $courseLessons;
        $courseLessons = array_values(array_filter($courseLessons, fn ($les) => $les['id'] !== $lesson->id));
        // dump($courseLessons,$lastCourseLessons);
        $set = false;


        for ($i = 0; $i < count($lastCourseLessons); $i++) {

            // if (isset($courseLessons[$i])) {
            if (($courseLessons[$i]['id'] === $pointerLesson->id) && !$set) {
                array_splice($courseLessons, ($i - $position) + 1, 0, [$lesson->only("id")]);
                $set = true;
                $i--;
                continue;
            }


            $courseLessons[$i]['exp_date_done'] = $lastCourseLessons[$i]['exp_date_done'];
            $courseLessons[$i]['last_exp_date'] = $lastCourseLessons[$i]['last_exp_date'];
            $courseLessons[$i]['index'] = $lastCourseLessons[$i]['index'];
            // }
        }

        return $courseLessons;
    }

    private function getOldestDate($date1, $date2, $tz): Carbon
    {
        $date1 = Carbon::parse($date1, $tz); //*TZ* ->tz($tz);
        $date2 = Carbon::parse($date2, $tz); //*TZ* ->tz($tz);
        // dump($date1->format("Y-m-d H:i:s"));

        if ($date1->isBefore($date2)) {
            return $date1;
        } else {
            return $date2;
        }
    }
}
