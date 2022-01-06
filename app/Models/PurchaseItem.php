<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = ["name", "quantity", "price", "notes"];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function receive_items()
    {
        return $this->hasMany(ReceiveItem::class);
    }
}
