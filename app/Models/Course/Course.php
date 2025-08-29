<?php

namespace App\Models\Course;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use function PHPUnit\Framework\isNull;

class Course extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = "mysql";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'notifs',
        'src_image',
        'private',
        'finish',
        'ratio',
        'done_days',
        'lessons_number',
        "weekend",
        "date_finish",
        'date_done',
        "inSpace",
        "have_single_lessons",
        "workDays",
        "last_activity",
        // "lesson_for_day",
        // "double_lessons"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'course_number',
        'done_days',
        'date_done',
        'lessons_number',
        'weekend',
        'private',
        'finish',
        'user_id',
        'notifs',
        "created_at",
        "updated_at",
        "inSpace",
        "last_activity",

        // "have_single_lessons",
        "workDays"
        // "lesson_for_day",
        // "double_lessons"
    ];

    protected $casts = [
        'private' => "boolean",
        'finish' => "boolean",
        'notifs' => "boolean",
        "inSpace" => "boolean",
        'ratio' => 'double'
        // "have_single_lessons" => "boolean"
    ];

    protected function ratio() : Attribute {
        return Attribute::get(function ($value) {
            return round($value,2);
        });
    }


    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            $course->activate();
        });

        static::updating(function (Course $course) {
            $course->activate();
        });
    }



    public function activate() {
        $this->last_activity = Carbon::now()->format("Y-m-d H:i:s");
    }

    // ======= Accessors and Mutators =======

    protected function weekend(): Attribute
    {


        return Attribute::make(
            set : function ($value)
            {
                if (is_null($value)) {
                    return null;
                }
                return implode(",", $value);
            },
            get : function ($value) {
                // var_dump($value);
                if (is_null($value) || (empty($value) && $value !== "0")) {
                    return [];
                }
                return explode(",", $value);
            }
        );
    }

    // ======= Relations =======
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}
