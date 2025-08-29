<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Board\Board;
use App\Models\Board\Liist;
use App\Models\Board\Task;
use App\Models\Course\Course;
use App\Models\Course\Lesson;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = "juzr_db";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // 'name',
        // 'email',
        // 'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'timezone',
        'updated_at',
        'id',
        'created_at',
        'email',
        'notifs_apps',
        'notifs_ads',
        'notifs_important'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    // ======= Relations =======

    // === Courses ===
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Course::class);
    }

    public function boards(): HasMany
    {
        return $this->hasMany(Board::class);
    }

    public function lists(): HasManyThrough
    {
        return $this->hasManyThrough(Liist::class, Board::class);
    }

    public function prefs(): HasOne
    {
        return $this->hasOne(Pref::class);
    }

    public function info(): HasOne
    {
        return $this->hasOne(Registered::class);
    }



    public function sessions(): HasMany {
        return $this->hasMany(Session::class);
    }

    public function session($id) {
        return $this->sessions()->where('id',$id)->first();
    }


    public function isRegistered()
    {
        return $this->info()->exists();
        // return DB::connection("juzr_db")->table('apps')->where("app_name",'nashit')->where("user_id", $this->id)->exists();
    }

    public function limits($limit_key = null, $select = null)
    {
        $limits = $this->info()->first()->only('courses_limit', 'boards_limit', 'lessons_limit', 'lists_limit', 'tasks_limit', 'days_done_limit', 'note_limit');
        if (!isset($limit_key)) {
            return collect($limits)->only($select);
        }

        return $limits[$limit_key];

    }

    public function registration()
    {
        $this->info()->create();
    }

    public function hook(): HasMany
    {
        return $this->hasMany(Hook::class);
    }

    public function checkHookKey($key)
    {
        $hook = $this->hook()->where('for', 'nashit')->first();
        if (!$hook) {
            return false;
        }
        if (Hash::check($key, $hook->key)) {
            $this->hook()->where('for', 'nashit')->delete();
            return true;
        }

        return false;
    }

    // public function registration() {
    //     $time = Carbon::now();
    //     DB::connection("juzr_db")->table('apps')->insert([
    //         "app_name" => "nashit",
    //         "user_id" => $this->id,
    //         "created_at" => $time,
    //         "updated_at" => $time,
    //     ]);
    // }
}
