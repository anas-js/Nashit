<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskController\CreateReq;
use App\Http\Requests\LessonsController\noteReq;
use App\Http\Requests\LessonsController\setReq;
use App\Http\Requests\TaskController\ChangeReq;
use App\Http\Requests\TaskController\MoveReq;
use App\Models\Board\Board;
use App\Models\Board\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function create(CreateReq $request, $id)
    {
        $user = $request->user();
        $list = app(ListController::class)->isExist($user, $id);
        $tz = $request->tz;

        if (($list->tasks_number + 1) > $user->limits('tasks_limit')) {
            throw ValidationException::withMessages([
                "Task" => "It is not possible to add more than {$user->limits('tasks_limit')} Task"
            ]);
        }


        $list->update([
            "tasks_number" => $list->tasks_number + 1,
        ]);
        $task = $list->tasks()->create([
            "name" => htmlspecialchars($request->name),
        ]);

        $task->date = [
            "create" => Carbon::parse($task->created_at)->tz($tz)->toAtomString(),
            "lastUpdate" => Carbon::parse($task->updated_at)->tz($tz)->toAtomString(),
            "done" => false
        ];

        $task->makeHidden("list", "updated_at", "created_at");





        $board = $list->board;
        $currRatio = round(100 / ($board->tasks_number+1) * ($board->tasks_done), 2);
        $board->update([
            "tasks_number" => $board->tasks_number + 1,
            "ratio" => $currRatio
        ]);



        // $task->list = $list;
        return $task;
    }

    public function remove(Request $request, $id = null,$taskDB = null)
    {
        $user = $request->user();
        if($id) {
            $task = $this->isExist($user, $id);
        } else {
            $task = $taskDB;
        }
        $isDone = $task->done;
        $task->delete();


        $list = $task->list;

        $tasks = $list->tasks()->orderBy("order", "asc");
        $tasks->each(function ($tk,$i) {
            $tk->update([
                "order" => $i+1
            ]);
        });

        $list->update([
            "tasks_number" => $list->tasks_number - 1,
        ]);

        $board = $list->board;
        $currRatio = 0;
        if(($board->tasks_number-1) != 0) {
            $currRatio = $isDone ? round((100 / ($board->tasks_number-1)) * ($board->tasks_done-1), 2) : round((100 / ($board->tasks_number-1)) * ($board->tasks_done), 2);
        }

        $board->update([
            "tasks_number" => $board->tasks_number - 1,
            "ratio" => $currRatio,
            "tasks_done" => $isDone ? $board->tasks_done-1 : $board->tasks_done
        ]);

        return ["msg" => "Task Removed Successfully!!"];
    }

    public function rename(CreateReq $request, $id = null,$taskDB =null)
    {
        $user = $request->user();
        if($id) {
            $task = $this->isExist($user, $id);
        } else {
            $task = $taskDB;
        }
        $newName = $request->name;

        $task->update([
            "name" => htmlspecialchars($newName)
        ]);

        return [
            "lastUpdate" => Carbon::parse($task->updated_at)->tz($request->tz)->toAtomString(),
        ];

        return ["msg" => "Task Renamed Successfully!!"];
    }
    public function note(noteReq $request, $id = null,$taskDB = null)
    {
        $user = $request->user();
        if($id) {
            $task = $this->isExist($user, $id);
        } else {
            $task = $taskDB;
        }

        $note = $request->note;

        $notePufter = clean($note) ?: null;

        if (strlen($notePufter) > 25000) {
            throw ValidationException::withMessages([
                "task" => "The note field must not be greater than 25000 Byte"
            ]);
        }


        $task->update([
            "note" => $notePufter
        ]);


        return [
            "lastUpdate" => Carbon::parse($task->updated_at)->tz($request->tz)->toAtomString(),
        ];



        return ["msg" => "task Notebook Changed Successfully!!"];
    }
    public function set(setReq $request, $id = null, $taskDB = null)
    {
        $user = $request->user();
        if($id) {
            $task = $this->isExist($user, $id);
        } else {
            $task = $taskDB;
        }
        $board =  $task->list->board;
        $set = $request->set;
        $tz = $request->tz;

        if ($task->done == $set) {
            return ["msg" => "Task Status Changed Successfully!!"];
        };



        $task->update([
            "done" => $set,
            "date_done" => $set ? Carbon::now() : null
        ]);



        $currRatio = $set ? round(100 / $board->tasks_number * ($board->tasks_done+1), 2) : round(100 / $board->tasks_number * ($board->tasks_done-1), 2);


        $board->update([
            "tasks_done" => $set ? $board->tasks_done + 1 : $board->tasks_done - 1,
            "ratio" => $currRatio
        ]);




        return [
            "create" => Carbon::parse($task->created_at)->tz($tz)->toAtomString(),
            "lastUpdate" => Carbon::parse($task->updated_at)->tz($tz)->toAtomString(),
            "done" => $set ? Carbon::now()->tz($tz)->toAtomString() : false
        ];


        // return ["msg" => "Task Status Changed Successfully!!"];
    }

    public function copy(Request $request, $id)
    {
        $user = $request->user();
        $task = $this->isExist($user, $id);
        $list = $task->list;
        $tz = $request->tz;

        if (($list->tasks_number + 1) > $user->limits('tasks_limit')) {
            throw ValidationException::withMessages([
                "Task" => "It is not possible to add more than {$user->limits('tasks_limit')} Task"
            ]);
        }

        $task->order = null;
        $task->done = false;
        $newTask = $list->tasks()->create($task->toArray());

        $newTask->date = [
            "create" => Carbon::parse($task->created_at)->tz($tz)->toAtomString(),
            "lastUpdate" => Carbon::parse($task->updated_at)->tz($tz)->toAtomString(),
            "done" => false
        ];

        $newTask->makeHidden("list", "updated_at", "created_at", "date_done", "note");

        $list->update([
            "tasks_number" => $list->tasks_number + 1,
        ]);

        $board = $list->board;

        $currRatio = round(100 / ($board->tasks_number+1) * ($board->tasks_done), 2);

        $board->update([
            "tasks_number" => $board->tasks_number + 1,
            "ratio" => $currRatio
        ]);

        return $newTask;
    }

    public function change(ChangeReq $request, $id)
    {
        $user = $request->user();
        $task = $this->isExist($user, $id);
        $oldList = $task->list;


        // $from = $request->from;
        $to = $request->to;
        $newIndex = $request->newIndex;
        // $lastListId = $task->list_id;

        if ($task->list_id != $to) {
            $newList = app(ListController::class)->isExist($user, $to);
        }



        $list = $newList ?? $oldList;

        // return $newIndex;

        // return [$task->order,$newIndex];
        // return [$list->tasks_number,$newIndex];
        if (($task->list_id != $to) || ($task->order != $newIndex)) {
            if ($list->tasks_number >= $newIndex) {
                $newIndex++;
                if ($task->list_id != $to) {
                    if($list->tasks_number >= $user->limits('tasks_limit')) {
                        throw ValidationException::withMessages([
                            "Task" => "It is not possible to add more than {$user->limits('tasks_limit')} Task"
                        ]);
                    }
                    $task->list_id = $to;

                    $newList->tasks()->where("order", ">=", $newIndex)->orderBy("order", "asc")->each(function ($t) {
                        // if (($t->order >= $newIndex)) {
                        $t->update([
                            "order" => $t->order + 1
                        ]);
                        // }
                    });

                    $newList->update([
                        "tasks_number" => $newList->tasks_number + 1
                    ]);

                    $oldList->tasks()->where("order", ">", $task->order)->orderBy("order", "asc")->each(function ($t) {
                        // if (($t->order >= $newIndex) && ($task->id !== $t->id )) {
                        $t->update([
                            "order" => $t->order - 1
                        ]);
                        // }
                    });

                    $oldList->update([
                        "tasks_number" => $oldList->tasks_number - 1
                    ]);
                } else {

                    $list->tasks()->orderBy("order", "asc")->each(function ($t) use ($newIndex, $task) {
                        if (($t->order <= $newIndex) && ($t->order > $task->order)) {
                            $t->update([
                                "order" => $t->order - 1
                            ]);
                        } else if (($t->order >= $newIndex) && ($t->order <= $task->order)) {
                            $t->update([
                                "order" => $t->order + 1
                            ]);
                        }
                    });
                }

                $list->update([
                    "updated_at" => now()
                ]);

                $task->order = $newIndex;
                $task->save();
            }
        }

        // return Carbon::now()->tz($request->tz)->toAtomString();


        return [
            "lastUpdate" => Carbon::parse($task->updated_at)->tz($request->tz)->toAtomString(),
        ];

        return ["msg" => "Task Changed Successfully!!"];
    }
    public function move(MoveReq $request, $id = null,$taskDB = null)
    {
        $user = $request->user();
        if($id) {
            $task = $this->isExist($user, $id);
        } else {
            $task = $taskDB;
        }
        $oldList = $task->list;
        $to = $request->to;

        if ($task->list_id != $to) {
            $newList = app(ListController::class)->isExist($user, $to);

            if($newList->tasks_number >= $user->limits('tasks_limit')) {
                throw ValidationException::withMessages([
                    "Task" => "It is not possible to add more than {$user->limits('tasks_limit')} Task"
                ]);
            }
            // $task->list_id = $to;

            $oldList->tasks()->where("order", ">", $task->order)->orderBy("order", "asc")->each(function ($t) {
                // $data = ;
                // if(!$dateUpdate) {
                //     $data["timestamp"] = false;
                //     var_dump($t->id);
                // }
                // if(!$dateUpdate) {
                //     $t->timestamps = false;


                // }
  // var_dump("UPDATE  $t->name TO NEW ORDER $t->order");

                $t->order = $t->order - 1;

                $t->save();

                // $t->update([
                //     "order" => $t->order - 1,
                // ]);
            });

            $oldList->update([
                "tasks_number" => $oldList->tasks_number - 1
            ]);


            $task->update([
                "list_id" => $to,
                 "order" => $newList->tasks_number + 1
            ]);

            $newList->update([
                "tasks_number" => $newList->tasks_number + 1
            ]);
        }

        return [
            "lastUpdate" => Carbon::parse($task->updated_at)->tz($request->tz)->toAtomString(),
        ];

        return ["msg" => "Task Moved Successfully!!"];
    }
    // =============== Helpers ===============
    public function isExist(User $user, $id): Task
    {

        $task = Task::whereHas("list", function ($list) use ($user) {
            $list->whereHas("board", function ($board) use ($user) {
                $board->where("user_id", $user->id);
            });
        })->find($id);

        if (!$task) {
            throw ValidationException::withMessages([
                "task" => 'task not found'
            ]);
        }

        $task->list->board->activate();

        return $task;
    }
}
