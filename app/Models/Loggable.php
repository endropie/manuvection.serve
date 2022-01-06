<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loggable extends Model
{
    protected $fillable = ['text'];

    protected static function booted()
    {
        static::creating(function ($loggable) {
            if (!auth()->user()) abort(403, 'Create log failed! Unauthorized.');
            $loggable->user_id = auth()->user()->id;
        });
    }
}
