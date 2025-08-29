<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pref extends Model
{
    use HasFactory;
    protected $connection = "juzr_db";
        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lang',
        'mode',
        'user_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
        "created_at",
        "updated_at",
        "id"
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
