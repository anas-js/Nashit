<?php

namespace App\Models\Board;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Board extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = "mysql";

    protected $fillable = [
        'name',
        'src_image',
        'ratio',
        'lists_number',
        'tasks_number',
        'tasks_done',
        'notifs',
        'user_id',
        "last_activity",
        'private',
    ];

    protected $hidden = [
        "board_number",
        'private',
        'user_id',
        // "created_at",
        "updated_at",
        "last_activity",
        'notifs',
    ];


    protected function ratio() : Attribute {
        return Attribute::get(function ($value) {
            return round($value,2);
        });
    }


    protected static function booted(): void
    {
        static::creating(function (Board $board) {
            $board->activate();
        });

        static::updating(function (Board $board) {
            $board->activate();
        });
    }



    public function activate() {
        $this->last_activity = Carbon::now()->format("Y-m-d H:i:s");
    }

    // ======= Relations =======
    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function lists() : HasMany {
        return $this->hasMany(Liist::class);
    }

    public function tasks() : HasManyThrough {
        return $this->hasManyThrough(Task::class, Liist::class,"board_id","list_id");
    }
}
