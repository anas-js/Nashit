<?php

namespace App\Models\Board;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Liist extends Model
{
    use HasFactory;
    protected $connection = "mysql";
    protected $table = "lists";

    protected $fillable = [
        "name",
        "tasks_number",
        "board_id",
        "updated_at"
    ];

    protected $hidden = [
        "board_id",
        "created_at",
        "updated_at",
    ];

    // ======= Relations =======
    public function board() : BelongsTo {
        return $this->belongsTo(Board::class);
    }

    public function tasks() : HasMany {
        return $this->hasMany(Task::class,"list_id");
    }
}
