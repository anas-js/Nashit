<?php

namespace App\Models\Board;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{

    use HasFactory;

           /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $connection = "mysql";
    protected $fillable = [
        'name',
        'done',
        'note',
        'date_done',
        'order',
        'list_id',
    ];

    // protected $hidden = [
    //     "list_id",
    // ];

    protected static function booted(): void
    {
        static::creating(function (Task $task) {
            if(!isset($task->order)) {
                $task->order = $task->list->tasks->count()+1;
            }
        });

    //     static::deleted(function (Task $task) {
    //         $tasks = $task->list->tasks()->orderBy("order", "asc");

    //         $tasks->each(function ($tk,$i) {
    //             $tk->update([
    //                 "order" => $i+1
    //             ]);
    //         });
    //    });
    }

    // ======= Relations =======
    public function list() : BelongsTo {
        return $this->belongsTo(Liist::class);
    }

    public function board() : BelongsTo {
        return $this->belongsTo(Board::class);
    }

}
