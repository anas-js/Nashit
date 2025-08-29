<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonsController\noteReq;
use App\Http\Requests\LessonsController\setReq;
use App\Http\Requests\ListController\copyReq;
use App\Http\Requests\ListController\renameReq;
use App\Http\Requests\ListController\UpdateReq;
use App\Http\Requests\TaskController\CreateReq;
use App\Http\Requests\TaskController\MoveReq;
use App\Models\Board\Liist;
use App\Models\Board\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ListController extends Controller
{
    public function remove(Request $request, $id)
    {
        $user = $request->user();
        $list = $this->isExist($user, $id);


        $board = $list->board;


        if ($board->lists_number === 1) {
            throw ValidationException::withMessages([
                "list" => "Cant Remove Last List"
            ]);
        }
        $tasks = $list->tasks()->get();
        $taskDone =  $tasks->whereIn('done', [true])->count();
        $list->tasks()->delete();
        $list->delete();

        $currRatio = round(100 / ($board->tasks_number - $tasks->count()) * ($board->tasks_done - $taskDone), 2);
        $board->update([
            "lists_number" => $board->lists_number - 1,
            'ratio' => $currRatio,
            "tasks_done" => $board->tasks_done - $taskDone,
            "tasks_number" => $board->tasks_number - $tasks->count()
        ]);


        return ["msg" => "List Removed Successfully!!"];
    }

    public function rename(renameReq $request, $id)
    {
        $user = $request->user();
        $list = $this->isExist($user, $id);
        $newName = $request->name;

        $list->update([
            "name" => htmlspecialchars($newName)
        ]);

        return [
            "lastUpdate" => Carbon::parse($list->updated_at)->tz($request->tz)->toAtomString(),
        ];

        return ["msg" => "List Renamed Successfully!!"];
    }
    public function copy(copyReq $request, $id)
    {
        $user = $request->user();
        $list = $this->isExist($user, $id);
        $board = $list->board;
        $tz = $request->tz;
        $type = $request->type;


        if (($board->lists_number + 1) > $user->limits('lists_limit')) {
            throw ValidationException::withMessages([
                "List" => "It is not possible to add more than {$user->limits('lists_limit')} Lists"
            ]);
        }

        $tasksAdded = 0;
        if ($type === "with-tasks") {
            $newList = $board->lists()->create($list->toArray());
            $tasks = $list->tasks->map(function ($task) {
                return collect($task)->except("done", "date_done");
            })->toArray();

            $newList->tasks()->createMany($tasks);
            $tasksAdded = count($tasks);
        } else {
            $list->tasks_number = 0;
            $newList = $board->lists()->create($list->toArray());
        }



        //* lists_number + tasks_number udate
        $board->update([
            "lists_number" => $board->lists_number + 1,
            "tasks_number" => $board->tasks_number + $tasksAdded
        ]);

        $newList->tasks = $newList->tasks()->limit(10)->get()->map(function ($task) use ($tz) {
            $task->date = [
                "create" => Carbon::parse($task->created_at)->tz($tz)->toAtomString(),
                "lastUpdate" =>  Carbon::parse($task->updated_at)->tz($tz)->toAtomString(),
                "done" => Carbon::parse($task->date_done)->tz($tz)->toAtomString()
            ];
            $task->makeHidden("date_done", "created_at", "updated_at");
            return $task;
        });

        $newList->date = [
            "create" => Carbon::parse($list->created_at)->tz($tz)->toAtomString(),
            "lastUpdate" =>   Carbon::parse($list->updated_at)->tz($tz)->toAtomString(),
        ];

        // ["msg" => "List Copied Successfully!"]
        return $newList;
    }
    public function create(renameReq $request, $id)
    {
        $user = $request->user();
        $board = app(BoardsController::class)->isExist($user, $id);
        $tz = $request->tz;
        $newName = $request->name;

        if (($board->lists_number + 1) > $user->limits('lists_limit')) {
            throw ValidationException::withMessages([
                "List" => "It is not possible to add more than {$user->limits('lists_limit')} Lists"
            ]);
        }

        $list = $board->lists()->create([
            "name" => htmlspecialchars($newName),
            "tasks_number" => 0
        ]);

        $list->tasks = [];

        $list->date = [
            "create" => Carbon::parse($list->created_at)->tz($tz)->toAtomString(),
            "lastUpdate" => Carbon::parse($list->updated_at)->tz($tz)->toAtomString(),
        ];



        $board->update([
            "lists_number" => $board->lists_number + 1,
        ]);



        return $list;
    }


    public function tasks(Request $request, $id)
    {
        $user = $request->user();
        $tz = $request->tz;


        if ($user) {
            $list = $this->isExist($user, $id);
        } else {
            $profile = $request->profile;
            $list = $this->isExist($profile, $id);
            if ($list->board->private) {
                throw ValidationException::withMessages([
                    "list" => 'list not found'
                ]);
            }
        }

        return $list->tasks()->orderBy('order')->simplePaginate(10)->through(function ($task) use ($tz) {
            $task->date = [
                "create" => Carbon::parse($task->created_at)->tz($tz)->toAtomString(),
                "lastUpdate" =>  Carbon::parse($task->updated_at)->tz($tz)->toAtomString(),
                "done" => Carbon::parse($task->date_done)->tz($tz)->toAtomString()
            ];
            $task->makeHidden("date_done", "created_at", "updated_at");
            return $task;
        });
    }

    public function update(UpdateReq $request)
    {

        // return $request->lists;
        $lists = collect($request->lists);
        $user = $request->user();
        $tz = $request->tz;


        // return 1;
        $canUpdateValue = function ($dateChanged, $lastUpdated) use ($tz) {
            if (!isset($dateChanged)) {
                return false;
            }
            $dateListLastUpdate = Carbon::parse($lastUpdated)->tz($tz);
            $dateChanged = Carbon::parse($dateChanged)->tz($tz);
            return $dateChanged->greaterThan($dateListLastUpdate);
        };

        $TaskController = app(TaskController::class);
        $boardId = null;





        $lists->each(function ($list) use ($user, $canUpdateValue, $request, $TaskController, &$boardId) {
            $listDB = $user->lists()->find($list['id']);
            $order = 1;
            $tasksExist =  collect([]);
            $reindex = false;
            if (!$listDB) {
                return;
            } else {
                if (!$boardId) {
                    $boardId = $listDB->board_id;
                }

                if (($listDB->board_id != $boardId)) {
                    return;
                }
            }

            if (isset($list['tasks'])) {
                $tasks = collect($list['tasks']);
                $tasks->each(function ($task) use (&$listDB, &$canUpdateValue, &$reindex) {
                    $taskDB = $listDB->tasks()->find($task['id']);

                    if (isset($task['process']['remove']) && ($taskDB) && $canUpdateValue($task['process']['remove'], $taskDB->updated_at)) {
                        $isDone = $taskDB->done;
                        $taskDB->delete();
                        $listDB->update([
                            "tasks_number" => $listDB->tasks_number - 1,
                        ]);

                        $board = $listDB->board;

                        $currRatio = 0;
                        if (($board->tasks_number - 1) != 0) {
                            $currRatio = $isDone ? round(100 / ($board->tasks_number - 1) * ($board->tasks_done - 1), 2) : round(100 / ($board->tasks_number - 1) * ($board->tasks_done), 2);
                        }


                        $board->update([
                            "tasks_number" => $board->tasks_number - 1,
                            "tasks_done" => $isDone ? $board->tasks_done - 1 : $board->tasks_done,
                            'ratio' => $currRatio
                        ]);
                        $reindex = true;
                    }
                });

                // echo $task->name;
                $tasks->each(function ($task) use (&$tasksExist, &$order, &$listDB, &$user, &$TaskController, &$request, &$canUpdateValue, &$reindex) {
                    if (isset($task['process']['remove'])) {
                        return;
                    }
                    $taskDB = $listDB->tasks()->find($task['id']);
                    // $taskAdded = false;
                    if (isset($task['process'])) {
                        $process = $task['process'];

                        $lastUp = null;
                        if (!$taskDB && isset($process['create'])) {
                            $newReq = new CreateReq();
                            $newReq->replace([
                                "name" => $task['name'],
                                "tz" => $request->tz
                            ]);
                            $newReq->setUserResolver(function () use ($user) {
                                return $user;
                            });

                            try {
                                $taskDB = $TaskController->create($newReq, $listDB->id);
                                unset($taskDB->date);
                                $lastUp = $process['create'];
                            } catch (ValidationException $e) {

                                return;
                            };
                        }

                        if ($taskDB) {
                            //  var_dump("task $taskDB->order list Tree Order {$listTree['order']}");
                            if (!$lastUp) {
                                $lastUp = $taskDB->updated_at;
                            }

                            if (isset($process['note']) && $canUpdateValue($process['note'], $lastUp)) {
                                $newReq = new noteReq();
                                $newReq->replace([
                                    "note" => $task['note'],
                                    "tz" => $request->tz
                                ]);

                                $newReq->setUserResolver(function () use ($user) {
                                    return $user;
                                });

                                try {
                                    $TaskController->note($newReq, taskDB: $taskDB);
                                } catch (ValidationException $e) {
                                };
                            }

                            if (isset($process['done']) && $canUpdateValue($process['done'], $lastUp)) {
                                // var_dump($task->taskValue['done']);
                                $newReq = new setReq();
                                $newReq->replace([
                                    "set" => $task['done'],
                                    "tz" => $request->tz
                                ]);

                                $newReq->setUserResolver(function () use ($user) {
                                    return $user;
                                });

                                $TaskController->set($newReq, taskDB: $taskDB);
                            }

                            if (isset($process['name']) && $canUpdateValue($process['name'], $lastUp)) {
                                $newReq = new CreateReq();
                                $newReq->replace([
                                    "name" => $task['name'],
                                ]);

                                $newReq->setUserResolver(function () use ($user) {
                                    return $user;
                                });

                                $TaskController->rename($newReq, taskDB: $taskDB);
                            }
                        }
                    }

                    if ($taskDB) {
                        if ($taskDB->order !==  $order) {
                            $taskDB->update([
                                "order" => $order
                            ]);
                            $reindex = true;
                        }

                        $order++;

                        $tasksExist->push($taskDB->id);
                    }
                });

                if ($reindex) {
                    $listDB->tasks()->whereNotIn("id", $tasksExist)->orderBy("order")->get()->each(function ($task) use ($list, &$order) {
                        $task->update([
                            "order" => $order
                        ]);

                        $order++;
                    });
                }
            }
        });

        // $listsLoops->each(function ($list) use ($user, $TaskController, $request) {
        //     $list['tasks']->each(function ($task) use ($user, $TaskController, $request, $list) {
        //         $task['db']->refresh();
        //         // dump($task->process['move']);
        //         $process = $task['process'];


        //         // if (isset($process['move'])) {
        //         //     // var_dump($process['move']['to']);
        //         //     // $newReq = new MoveReq();
        //         //     // $newReq->replace([
        //         //     //     "to" => $process['move']['to'],
        //         //     //     "tz" => $request->tz
        //         //     // ]);


        //         //     // $newReq->setUserResolver(function () use ($user) {
        //         //     //     return $user;
        //         //     // });

        //         //     // $TaskController->move($newReq, taskDB: $task['db']);
        //         //     // $newList = null;
        //         //     // $newListId = $process['move']['to'];
        //         //     // try {
        //         //     //     $newList = $this->isExist($user,$newListId);
        //         //     // } catch (ValidationException $e){

        //         //     // }
        //         //     $oldList = $task['db']->list->refresh();
        //         //     // var_dump($oldList);

        //         //     // var_dump("old LIST IS $oldList->tasks_number -1");
        //         //     $oldList->update([
        //         //         "tasks_number" => $oldList->tasks_number - 1
        //         //     ]);
        //         //     // $oldList->tasks_number = $oldList->tasks_number - 1;
        //         //     // var_dump("{$task['db']} +1");
        //         //     $task['db']->update([
        //         //         "list_id" => $list['db']->id,
        //         //     ]);

        //         //     $list['db']->refresh();

        //         //     // var_dump("old LIST IS {$list['db']->tasks_number} +1");
        //         //     $list['db']->update([
        //         //         "tasks_number" => $list['db']->tasks_number + 1
        //         //     ]);

        //         //     // $list['db']->tasks_number + 1;
        //         //     // $list['db']->tasks_number = $list['db']->tasks_number + 1;
        //         // }

        //         if (isset($process['order'])) {
        //             // $task['orderUPDATe'] = $process['order'];
        //             $task['db']->update([
        //                 "order" => $process['order']
        //             ]);
        //         }
        //     });



        //     // $list['index'] = $index;
        // });
        // return ["ok" => "UPDATE"];
        // return ["lists"=> $listsLoops];


        // $lists->each(function ($list) use ($tz,$user, $canUpdateValue, $request) {
        //     $order = 1;
        //     $reindex = false;
        //     $taskChangingId = collect([]);
        //     $listDB = $user->lists()->find($list['id']);
        //     if (!$listDB) {
        //         return;
        //     }

        //     $TaskController = app(TaskController::class);
        //     // echo $task->name;
        //     collect($list['tasks'])->each(function ($task) use ($tz,&$listDB, &$user, &$TaskController, &$request, &$reindex, &$canUpdateValue, &$order, &$taskChangingId) {

        //         $taskDB = $listDB->tasks()->find($task['id']);


        //         if (isset($task['process'])) {

        //             $process = $task['process'];

        //             // dump($taskDB->id,$taskDB->list_id);
        //             $move = null;

        //             // dump($process['move']['to']);
        //             if (!$taskDB && isset($process['move']['to'], $process['move']['time'])) {

        //                 $taskFrom = null;

        //                 try {
        //                     $taskFrom = $TaskController->isExist($user, $task['id']);
        //                 } catch (ValidationException $e) {
        //                 };


        //                 // dd($taskFrom);

        //                 if ($taskFrom && ($taskFrom->list_id != $process['move']['to'])) {

        //                     // dump($taskFrom->id);
        //                     $taskDB = $taskFrom;
        //                     //  var_dump($taskDB->name);
        //                     // var_dump("CAN MOVE");
        //                     // var_dump($canUpdateValue($process['move']['time'] , $taskDB->updated_at));
        //                     // var_dump(
        //                     // Carbon::parse($process['move']['time'])->tz($tz)->format("Y-m-d\TH:i:s.u\Z"),
        //                     // $taskDB->updated_at->tz($tz)->format("Y-m-d\TH:i:s.u\Z"));
        //                     // var_dump(Carbon::parse($process['move']['time'],$tz)->greaterThan($taskDB->updated_at->tz($tz)));
        //                     //   var_dump($taskDB->updated_at->tz($tz)->format("Y-m-d\TH:i:s.u\Z"));
        //                     //   var_dump($process['move']['time']);
        //                     //   var_dump($canUpdateValue($process['move']['time'], $taskDB->updated_at));
        //                     if ($canUpdateValue($process['move']['time'], $taskDB->updated_at)) {
        //                         $move = $process['move'];
        //                     }
        //                 }
        //                 // $listTo =  $user->lists()->find($process['move']['to']);
        //                 // if($listTo) {
        //                 //     $taskDB = $listTo->tasks()->find($task['id']);
        //                 //     dd($listFrom->id);
        //                 //     if($taskDB && ($taskDB->list_id === $listFrom->id)) {
        //                 //         $taskDB = null;
        //                 //     } else if ($taskDB && $taskDB->list_id !== $listFrom->id) {
        //                 //         $move = $process['move'];
        //                 //     }
        //                 // }
        //             }


        //             $lastUp = null;
        //             if (!$taskDB) {
        //                 if (isset($process['create']) && !isset($process['remove'])) {
        //                     $newReq = new CreateReq();
        //                     $newReq->replace([
        //                         "name" => $task['name'],
        //                         "tz" => $request->tz
        //                     ]);
        //                     $newReq->setUserResolver(function () use ($user) {
        //                         return $user;
        //                     });

        //                     try {
        //                         $taskDB = $TaskController->create($newReq, $listDB->id);
        //                         $lastUp = $process['create'];
        //                     } catch (ValidationException $e) {
        //                     };
        //                 } else {
        //                     return;
        //                 }

        //                 // return;
        //             }

        //             if (!$lastUp) {
        //                 $lastUp = $taskDB->updated_at;
        //             }


        //             if (isset($process['remove']) && $canUpdateValue($process['remove'], $lastUp)) {
        //                 $newReq = new Request();
        //                 $newReq->setUserResolver(function () use ($user) {
        //                     return $user;
        //                 });

        //                 $TaskController->remove($newReq, taskDB: $taskDB);
        //             }

        //             if (isset($process['done']) && $canUpdateValue($process['done'], $lastUp)) {
        //                 $newReq = new setReq();
        //                 $newReq->replace([
        //                     "set" => $task['done'],
        //                     "tz" => $request->tz
        //                 ]);

        //                 $newReq->setUserResolver(function () use ($user) {
        //                     return $user;
        //                 });

        //                 $TaskController->set($newReq, taskDB: $taskDB);
        //             }

        //             if (isset($process['note']) && $canUpdateValue($process['note'], $lastUp)) {
        //                 $newReq = new noteReq();
        //                 $newReq->replace([
        //                     "note" => $task['note'],
        //                     "tz" => $request->tz
        //                 ]);

        //                 $newReq->setUserResolver(function () use ($user) {
        //                     return $user;
        //                 });

        //                 try {
        //                     $TaskController->note($newReq, taskDB: $taskDB);
        //                 } catch (ValidationException $e) {
        //                 };
        //             }

        //             if (isset($process['name']) && $canUpdateValue($process['name'], $lastUp)) {
        //                 $newReq = new CreateReq();
        //                 $newReq->replace([
        //                     "name" => $task['name'],
        //                 ]);

        //                 $newReq->setUserResolver(function () use ($user) {
        //                     return $user;
        //                 });

        //                 $TaskController->rename($newReq, taskDB: $taskDB);
        //             }

        //             if ($move) {
        //                 // var_dump("move");
        //                 $newReq = new MoveReq();
        //                 $newReq->replace([
        //                     "to" => $move['to'],
        //                     "tz" => $request->tz
        //                 ]);


        //                 $newReq->setUserResolver(function () use ($user) {
        //                     return $user;
        //                 });
        //                 // $reindex = true;

        //                 // var_dump('move',$move['to']);

        //                 $TaskController->move($newReq, taskDB: $taskDB,dateUpdate: false);
        //             }
        //         }
        //         if ($taskDB) {
        //             // var_dump($taskDB->order !== $order);
        //             if ($taskDB->order !== $order) {
        //                 // var_dump($taskDB->name);
        //                 // var_dump("SET",$order);
        //                 $taskChangingId->push($taskDB->id);
        //                 $reindex = true;

        //                 // var_dump("UPDATE");
        //                 $taskDB->update([
        //                     "order" => $order
        //                 ]);
        //             }
        //             $taskChangingId->push($taskDB->id);
        //             $order++;
        //         }
        //     });

        //     // var_dump("last");
        //     if ($reindex) {
        //         $listDB->tasks()->whereNotIn("id", $taskChangingId)->orderBy("order")->get()->each(function ($task) use (&$order) {
        //             // var_dump("UPDATE $task->name");
        //             $task->order = $order;
        //             $task->timestamps = false;
        //             $task->save();

        //             // $task->update([
        //             //     "order" => $order
        //             // ]);

        //             $order++;
        //         });
        //     }
        // });

        return ["SYNC" => "OK"];

        // return

        // dd($reindex->toArray());
    }
    // =============== Helpers ===============
    public function isExist(User $user, $id): Liist
    {
        $list = $user->lists()->find($id);
        if (!$list) {
            throw ValidationException::withMessages([
                "list" => 'list not found'
            ]);
        }

        $list->board->activate();
        return $list;
    }
}
