<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    protected $fillable = ["name", "quantity", "notes"];

    public function Bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function base()
    {
        return $this->morphTo();
    }
}
