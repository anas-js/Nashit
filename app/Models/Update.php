<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'version',
        'description',
        'date',
        'added',
        'removed',
        'note_limit'
    ];
    protected $hidden = [
        'updated_at',
        'created_at',
        "id"
    ];

    protected $casts = [
        'added' => 'array'
    ];

    protected function added() : Attribute {
        return Attribute::get(function ($value) {
            return json_decode($value)->value ?? [];
        });
    }

    protected function removed() : Attribute {
        return Attribute::get(function ($value) {
            return json_decode($value)->value ?? [];
        });
    }
}
