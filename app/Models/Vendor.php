<?php

namespace App\Models;

use App\Traits\HasLoggable;
use Endropie\ApiToolkit\Traits\HasFilterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, HasFilterable, HasLoggable, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'email', 'phone', 'address'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function receives()
    {
        return $this->hasMany(Receive::class);
    }

    public function purchase_items()
    {
        return $this->hasManyThrough(PurchaseItem::class, Purchase::class);
    }

    public function receive_items()
    {
        return $this->hasManyThrough(ReceiveItem::class, Receive::class);
    }
}
