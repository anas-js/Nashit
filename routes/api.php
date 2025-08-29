<?php

use App\Http\Controllers\BoardsController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\GlobalContoller;
use App\Http\Controllers\HooksController;
use App\Http\Controllers\LessonsController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Route::middleware('auth')->get('/user', function (Request $request) {
//     return $request->user();
// });




// Auth User
Route::middleware(['auth', 'regist'])->group(function () {
    //  ================ /start ===============
    Route::prefix("start")->group(function () {
        Route::post("board", [BoardsController::class, "create"]);
        Route::post("course", [CoursesController::class, "create"]);
    });


    //  ================ /courses ===============


    Route::get("/courses", [CoursesController::class, "get"]);
    //  ================ /course ===============
    Route::get("/course/{id}", [CoursesController::class, "info"])->where("id", "^[a-z0-9\-]{36}$");

    Route::post("/course/{id}/upload", [CoursesController::class, "upload"])->where("id", "^[a-z0-9\-]{36}$");

    Route::get("/course/{id}/lessons", [CoursesController::class, "lessons"])->where("id", "^[a-z0-9\-]{36}$");
    Route::get("/course/{id}/lessons/today", [CoursesController::class, "today"])->where("id", "^[a-z0-9\-]{36}$");
    Route::post("/course/{id}/lessons/add", [CoursesController::class, "add"])->where("id", "^[a-z0-9\-]{36}$");
    Route::post("/course/{id}/lessons/redist", [CoursesController::class, "redist"])->where("id", "^[a-z0-9\-]{36}$");
    Route::get("/course/{id}/finish", [CoursesController::class, "finish"])->where("id", "^[a-z0-9\-]{36}$");
    Route::post("/course/{id}/settings", [CoursesController::class, "settings"])->where("id", "^[a-z0-9\-]{36}$");
    Route::get("/course/{id}/stats", [CoursesController::class, "stats"])->where("id", "^[a-z0-9\-]{36}$");
    Route::post("/course/{id}/copy", [CoursesController::class, "copy"])->where("id", "^[a-z0-9\-]{36}$");
    Route::post("/course/{id}/extra", [CoursesController::class, "extra"])->where("id", "^[a-z0-9\-]{36}$");
    Route::post("/course/update", [CoursesController::class, "update"]);

    //  ================ /lesson ===============
    Route::post("/lesson/{id}/rename", [LessonsController::class, "rename"])->where("id", "^\d{1,19}$");
    Route::post("/lesson/{id}/set", [LessonsController::class, "set"])->where("id", "^\d{1,19}$");
    Route::post("/lesson/{id}/note", [LessonsController::class, "note"])->where("id", "^\d{1,19}$");
    Route::post("/lesson/{id}/remove", [LessonsController::class, "remove"])->where("id", "^\d{1,19}$");
    Route::post("/lesson/{id}/move", [LessonsController::class, "move"])->where("id", "^\d{1,19}$");
    Route::post("/lesson/{id}/copy", [LessonsController::class, "copy"])->where("id", "^\d{1,19}$");

    //  ================ /boards ===============
    Route::get("/boards", [BoardsController::class, "get"]);

    //  ================ /board ===============
    Route::get("/board/{id}", [BoardsController::class, "info"])->where("id", "^[a-z0-9\-]{36}$");
    Route::post("/board/{id}/upload", [BoardsController::class, "upload"])->where("id", "^[a-z0-9\-]{36}$");
    Route::get("/board/{id}/stats", [BoardsController::class, "stats"])->where("id", "^[a-z0-9\-]{36}$");
    Route::post("/board/{id}/settings", [BoardsController::class, "settings"])->where("id", "^[a-z0-9\-]{36}$");
    Route::post("/board/{id}/copy", [BoardsController::class, "copy"])->where("id", "^[a-z0-9\-]{36}$");
    Route::get("/board/{id}/lists", [BoardsController::class, "lists"])->where("id", "^[a-z0-9\-]{36}$");
    Route::post("/board/{id}/list/create", [ListController::class, "create"])->where("id", "^[a-z0-9\-]{36}$");
    //  ================ /list ===============
    Route::post("/list/{id}/remove", [ListController::class, "remove"])->where("id", "^\d{1,19}$");
    Route::post("/list/{id}/rename", [ListController::class, "rename"])->where("id", "^\d{1,19}$");
    Route::post("/list/{id}/copy", [ListController::class, "copy"])->where("id", "^\d{1,19}$");
    Route::get("/list/{id}/tasks", [ListController::class, "tasks"])->where("id", "^\d{1,19}$");
    Route::post("/list/{id}/task/create", [TaskController::class, "create"])->where("id", "^\d{1,19}$");
    Route::post("/list/update", [ListController::class, "update"]);
    //  ================ /task ===============
    Route::post("/task/{id}/remove", [TaskController::class, "remove"])->where("id", "^\d{1,19}$");
    Route::post("/task/{id}/rename", [TaskController::class, "rename"])->where("id", "^\d{1,19}$");
    Route::post("/task/{id}/note", [TaskController::class, "note"])->where("id", "^\d{1,19}$");
    Route::post("/task/{id}/set", [TaskController::class, "set"])->where("id", "^\d{1,19}$");
    Route::post("/task/{id}/copy", [TaskController::class, "copy"])->where("id", "^\d{1,19}$");
    Route::post("/task/{id}/change", [TaskController::class, "change"])->where("id", "^\d{1,19}$");
    Route::post("/task/{id}/move", [TaskController::class, "move"])->where("id", "^\d{1,19}$");

    //  ================ /prefs ===============


    Route::get("/limits", [UserController::class, "limits"]);

    //* TEST

});

Route::middleware('auth')->post("/registration", [UserController::class, "registration"]);

// profile
Route::middleware("profile")->prefix('profile')->group(function () {

    Route::get("/{username}/courses", [CoursesController::class, "get"])->where("username", "^@[A-Za-z0-9\_]{3,9}$");
    Route::get("/{username}/course/{id}", [CoursesController::class, "info"])->where(["id" => "^[a-z0-9\-]{36}$", "username", "^@[A-Za-z0-9_]{3,9}$"]);
    Route::get("/{username}/course/{id}/lessons", [CoursesController::class, "lessons"])->where(["id" => "^[a-z0-9\-]{36}$", "username", "^@[A-Za-z0-9_]{3,9}$"]);
    Route::get("/{username}/course/{id}/lessons/today", [CoursesController::class, "today"])->where(["id" => "^[a-z0-9\-]{36}$", "username", "^@[A-Za-z0-9_]{3,9}$"]);
    Route::get("/{username}/course/{id}/finish", [CoursesController::class, "finish"])->where(["id" => "^[a-z0-9\-]{36}$", "username", "^@[A-Za-z0-9_]{3,9}$"]);
    Route::get("/{username}/course/{id}/stats", [CoursesController::class, "stats"])->where(["id" => "^[a-z0-9\-]{36}$", "username", "^@[A-Za-z0-9_]{3,9}$"]);

    Route::get("/{username}/boards", [BoardsController::class, "get"])->where("username", "^@[A-Za-z0-9\_]{3,9}$");
    Route::get("/{username}/board/{id}", [BoardsController::class, "info"])->where(["id" => "^[a-z0-9\-]{36}$", "username", "^@[A-Za-z0-9_]{3,9}$"]);
    Route::get("/{username}/board/{id}/lists", [BoardsController::class, "lists"])->where(["id" => "^[a-z0-9\-]{36}$", "username", "^@[A-Za-z0-9_]{3,9}$"]);
    Route::get("/{username}/board/{id}/stats", [BoardsController::class, "stats"])->where(["id" => "^[a-z0-9\-]{36}$", "username", "^@[A-Za-z0-9_]{3,9}$"]);

    Route::get("/{username}/list/{id}/tasks", [ListController::class, "tasks"])->where("id", "^\d{1,19}$");
});

Route::get("/updates", [GlobalContoller::class, "updates"]);

// User
Route::post("/user/settings", [HooksController::class, "settings"]);




// Route::middleware('cache.headers:public')->group(function () {
//     Route::get("/test", function () {
//         return ["msg"=> "gooo"];
//     });
//  });


// Route::get("/test", function (Request $request) {

    // $users =        User::whereHas('info', function (Builder $query) {
    //     $query->from('nashit.registered');
    // })->whereHas('courses', function (Builder $query) {
    //     $query->from('nashit.courses')->where('finish', false)->where('notifs', true)->whereHas('lessons', function ($query) {
    //         $query->from('nashit.lessons')->where(function ($q) {
    //             $q->whereRaw('DATE(exp_date_done) = DATE(CONVERT_TZ("2024-07-31","+00:00",users.timezone))')->orWhere(function ($q) {
    //                 $q->whereRaw('DATE(exp_date_done) < DATE(CONVERT_TZ("2024-07-31","+00:00",users.timezone))')->where('done', false);
    //             });
    //         });
    //     });
    // })->whereRaw('DATE_FORMAT(CONVERT_TZ(now(),"+00:00",users.timezone),"%H") = 2')->with('prefs')->get();

    // foreach ($users as $user) {
    //     $courses = $user->courses()->where('finish', false)->where('notifs', true)->whereHas('lessons', function ($query) use ($user) {
    //         $query->where(function ($q) use ($user) {
    //             $q->whereRaw("DATE(exp_date_done) = DATE(CONVERT_TZ('2024-07-31','+00:00','$user->timezone'))")->orWhere(function ($q) use ($user) {
    //                 $q->whereRaw("DATE(exp_date_done) < DATE(CONVERT_TZ('2024-07-31','+00:00','$user->timezone'))")->where('done', false);
    //             });
    //         });
    //     })->with(['lessons' => function ($query) use ($user) {
    //         $query->where(function ($q) use ($user) {
    //             $q->whereRaw("DATE(exp_date_done) = DATE(CONVERT_TZ('2024-07-31','+00:00','$user->timezone'))")->orWhere(function ($q) use ($user) {
    //                 $q->whereRaw("DATE(exp_date_done) < DATE(CONVERT_TZ('2024-07-31','+00:00','$user->timezone'))")->where('done', false);
    //             });
    //         });
    //     }])->get();

    //     dump($courses->toArray());
    // }



//     return 1;
// });
