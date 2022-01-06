<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiveItem extends Model
{
    protected $fillable = ["name", "quantity", "notes"];

    public function receive()
    {
        return $this->belongsTo(Receive::class);
    }

    public function purchase_item()
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function bill_item()
    {
        return $this->morphOne(BillItem::class, 'base');
    }
}
