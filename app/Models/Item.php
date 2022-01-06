<?php

namespace App\Models;

use App\Traits\HasLoggable;
use Endropie\ApiToolkit\Traits\HasFilterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, HasFilterable, HasLoggable, SoftDeletes;

    protected $fillable = [
        'code', 'name'
    ];
}
