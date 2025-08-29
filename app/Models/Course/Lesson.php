<?php

namespace App\Models\Course;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use HasFactory;
    protected $connection = "mysql";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'done',
        'note',
        'date_done',
        'exp_date_done',
        'index',
        'last_exp_date',
        'course_id',
    ];

    protected $casts = [
        'done' => "boolean"
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        'course_id',
        'last_exp_date'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Lesson $lesson) {
            if(!isset($lesson->index)) {
                $lesson->index = $lesson->course->lessons->count()+1;
            }
        });

        static::deleted(function (Lesson $lesson) {
            $lessons = $lesson->course->lessons()->orderByRaw("DATE(exp_date_done) ASC, `index` ASC");
            //  dd($lessons->get()->toArray());

            $lessons->each(function ($les,$i) {
                $les->update([
                    "index" => $i+1
                ]);
            });
       });
    }




    // ======= Relations =======
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
