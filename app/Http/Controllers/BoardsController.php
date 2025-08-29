<?php

namespace App\Http\Controllers;

use App\Helpers\ImageUploader;
use App\Http\Requests\BoardsController\GetReq;
use App\Http\Requests\BoardsController\CreateReq;
use App\Http\Requests\BoardsController\SettingsReq;
use App\Http\Requests\BoardsController\UploadReq;
use App\Models\Board\Board;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BoardsController extends Controller
{

    public function create(CreateReq $request)
    {
        $user = $request->user();
        $boardLength = $user->boards()->count();

        if (($boardLength + 1) > $user->limits('boards_limit')) {
            throw ValidationException::withMessages([
                "boards" => "you account have {$user->limits('boards_limit')} board!!"
            ]);
        }

        $board = $request->user()->boards()->create([
            "name" => htmlspecialchars($request->name),
            'notifs' => $user->notifs_apps
        ]);

        $list = $board->lists()->create([
            "name" => "ToDo List"
        ]);

        $list->tasks()->create([
            "name" => "Task",
        ]);




        return response()->json(["id" => $board->id]);
    }
    // get All Boards For User
    public function get(GetReq $request)
    {
        $user = $request->user();
        $tz = $request->tz;

        // $boards = $user->boards();
        // $order = $request->order;

        // return ['tz'=> $request];



        // if ($order) {
        //     switch ($order) {
        //         case ('new-old'): {
        //                 $boards->orderBy("created_at", "desc");
        //                 break;
        //             }
        //         case ('old-new'): {
        //                 $boards->orderBy("created_at", "asc");
        //                 break;
        //             }
        //         case ('last-active'): {
        //                 $boards->orderBy("last_activity", "desc");
        //                 break;
        //             }
        //         case ('ratio'): {
        //                 $boards->orderBy("ratio", "desc");
        //                 break;
        //             }
        //     }
        // }


        if ($user) {
            $boards = $user->boards();
            $order = $request->order;

            if ($order) {
                switch ($order) {
                    case ('new-old'): {
                            $boards->orderBy("created_at", "desc");
                            break;
                        }
                    case ('old-new'): {
                            $boards->orderBy("created_at", "asc");
                            break;
                        }
                    case ('last-active'): {
                            $boards->orderBy("last_activity", "desc");
                            break;
                        }
                    case ('ratio'): {
                            $boards->orderBy("ratio", "desc");
                            break;
                        }
                }
            } else {
                $boards->orderBy("created_at", "desc");
            }
        } else {
            $boards = $request->profile->boards()->where("private", false);
        }

        // var_dump($user->timezone);
        return $boards->simplePaginate(6)->through(function ($board) use ($tz) {
            $board->makeVisible(['private']);
            $b = collect($board);
            $b["date"] = Carbon::parse($b["created_at"])->tz($tz)->format("Y-m-d");
            $b['details'] = [
                "sub" => $b["lists_number"],
                "task" => $b["tasks_number"],
                "done" => $b["tasks_done"]
            ];

            return $b->except("lists_number", "tasks_number", "tasks_done", "created_at");
        });
    }

    public function stats(Request $request, $id)
    {
        $user = $request->user();
        $tz = $request->tz;
        if ($user) {
            $board = $this->isExist($user, $id);
        } else {
            $profile = $request->profile;
            $board = $this->IsAvailable($profile, $id);
        }

        $days = Carbon::parse($board->created_at)->startOfDay()->diffInDays(Carbon::now($tz)->startOfDay());
        return [
            "ratio" => $board->ratio,
            "day" => $days
        ];
    }

    public function settings(SettingsReq $request, $id)
    {

        $user = $request->user();
        $board = $this->isExist($user, $id);


        if ($request->delete) {
            $board->tasks()->delete();
            $board->lists()->delete();
            if (Storage::disk('boards')->exists($board->id . '.jpg')) {
                Storage::disk('boards')->delete($board->id . '.jpg');
            }
            $board->delete();

            return [
                "msg" => "Board Deleted Successfully!"
            ];
        } else {
            if ($request->has("name")) {
                $request->merge(["name" => htmlspecialchars($request->name)]);
            }

            if ($request->has("notifs")) {
                if($request->notifs && !$user->notifs_apps) {
                    throw new HttpResponseException(response()->json([
                        'account' => 'Sending to email is turned off in the account settings',
                        'code' => 1
                    ],404));
                }

                $board->update([
                    'notifs' => $request->notifs
                ]);
            }

            $board->update($request->only("private", "name"));



            return [
                "msg" => "Board Modified Successfully!"
            ];
        }
    }

    public function info(Request $request, $id)
    {
        $user = $request->user();

        if ($user) {
            $board = $this->isExist($user, $id);
        } else {
            $profile = $request->profile;
            $board = $this->IsAvailable($profile, $id);
        }

        return [
            "title" =>  $board->name,
            "data" => [
                "notifs" => $board->notifs,
                "private" => $board->private,
                "finish" => $board->finish,
                "tasks_limit" =>  isset($user) ? $user->limits("tasks_limit") : null,
                "lists_limit" =>  isset($user) ? $user->limits("lists_limit") : null,
                "note_limit" =>  isset($user) ? $user->limits("note_limit") : null,
                "id" => $board->id
            ]
        ];
    }

    public function copy(Request $request, $id)
    {
        $user = $request->user();
        $withTasks = $request->type === 'with-tasks';

        $boardLength = $user->boards()->count();
        if (($boardLength + 1) > $user->limits('boards_limit')) {
            throw ValidationException::withMessages([
                "board" => "you account have {$user->limits('boards_limit')} board!!"
            ]);
        }

        $board = Board::where("private", false)->find($id) ?? $user->boards()->find($id);

        if (!$board) {
            throw ValidationException::withMessages([
                "board" => 'board not found'
            ]);
        }

        if ($withTasks) {
            $newBoard = $user->boards()->create([
                "name" => $board->name,
                "ratio" => $board->ratio,
                "lists_number" => $board->lists_number,
                "tasks_number"  => $board->tasks_number,
                "tasks_done" => $board->tasks_done
            ]);


        } else {
            $newBoard = $user->boards()->create([
                "name" => $board->name,
                "lists_number" => $board->lists_number,
                "tasks_number"  => 0,
            ]);
        }





        $board->lists->each(function ($list) use ($newBoard,$withTasks) {
            $newList = $newBoard->lists()->create($list->toArray());
            if($withTasks) {
                $list->tasks->each(function ($task) use ($newList) {
                    $newList->tasks()->create($task->toArray());
                });
            }
        });


        return ["id" => $newBoard->id];
    }
    public function lists(Request $request, $id)
    {
        $user = $request->user();
        $tz = $request->tz;
        if ($user) {
            $board = $this->isExist($user, $id);
        } else {
            $profile = $request->profile;
            $board = $this->IsAvailable($profile, $id);
        }

        $lists = $board->lists()->get();

        $lists->map(function ($list) use ($tz) {
            $list->tasks = $list->tasks()->limit(10)->orderBy('order')->get()->map(function ($task) use ($tz) {
                $task->date = [
                    "create" => Carbon::parse($task->created_at)->tz($tz)->toAtomString(),
                    "lastUpdate" =>  Carbon::parse($task->updated_at)->tz($tz)->toAtomString(),
                    "done" => Carbon::parse($task->date_done)->tz($tz)->toAtomString()
                ];
                $task->makeHidden("date_done", "created_at", "updated_at");
                return $task;
            });

            $list->date = [
                "create" => Carbon::parse($list->created_at)->tz($tz)->toAtomString(),
                "lastUpdate" =>   Carbon::parse($list->updated_at)->tz($tz)->toAtomString(),
            ];
            return $list;
        });

        // return Carbon::parse("2024-06-08 18:22:35")->tz($tz)->toAtomString();
        return [
            "lists" => $lists
        ];
    }
    public function upload(UploadReq $request, $id)
    {

        $user = $request->user();
        $board = $this->isExist($user, $id);
        $image = $request->image;
        $imageSize = $image->getSize() / 1024 / 1024;

        $x = $request->x;
        $y = $request->y;
        $width = $request->width;
        $height = $request->height;


        $imageStore = ImageUploader::proces($image, [$x, $y, $width, $height], [230, 100],2.3, 2);


        Storage::disk('boards')->put($id . '.jpg', $imageStore);

        return ['image' => 'image Upload Done'];
    }
    public function image(Request $request, $id)
    {

        $user =  Auth::user();
        $board = Board::where("private", false)->find($id);

        if (!$board && $user) {
            $board = $user->boards()->find($id);
        }



        if (!$board || !Storage::disk('boards')->exists($board->id . '.jpg')) {
            abort(404,'Image Not Found');
        }



        return response(Storage::disk('boards')->get($board->id . '.jpg'))->header('Content-Type', 'image/jpeg');



        // $course = $this->IsAvailable($profile, $id);

        // if ($user) {
        //     $course = $this->isExist($user, $id);
        // } else {
        //     $profile = $request->profile;

        // }


    }


    // =============== Helpers ===============

    // Chick if User Have This board, if valid return
    public function isExist(User $user, $id): Board
    {
        $board =  $user->boards()->find($id);
        if (!$board) {
            throw ValidationException::withMessages([
                "board" => 'board not found'
            ]);
        }

        $board->activate();
        return $board;
    }

    private function isAvailable($profile, $id)
    {
        $board = $profile->boards()->where("private", false)->find($id);

        if (!$board) {
            throw ValidationException::withMessages([
                "board" => 'board not found'
            ]);
        }

        return $board;
    }
}
