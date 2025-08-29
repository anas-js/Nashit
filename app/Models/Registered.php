<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registered extends Model
{
    use HasFactory;
    protected $table = "registered";
    protected $connection = "mysql";

    protected $fillable = [
        'courses_limit',
        'lessons_limit',
        'boards_limit',
        'lists_limit',
        'tasks_limit',
        'days_done_limit',
        'note_limit'
    ];
    protected $hidden = [
        'user_id',
        'updated_at',
        'created_at',
        "id"
    ];
}
