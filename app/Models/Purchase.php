<?php

namespace App\Models;

use App\Traits\HasLoggable;
use App\Traits\HasNextNumber;
use Endropie\ApiToolkit\Traits\HasFilterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasNextNumber, HasLoggable, HasFilterable;

    protected $fillable = ["vendor_id", "number", "date", "due", "description"];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getAmountAttribute(): float
    {
        return (double) $this->items()->selectRaw('quantity * price as amount')->get()->sum('amount');
    }

    public static function getConfigNextNumber()
    {
        return [
            "column" => 'number',
            "prefix" => config('setup.purchase.number.prefix', 'PO'),
            "period" => config('setup.purchase.number.period', 'Y-m'),
            "digits" => config('setup.purchase.number.digits', 5),
            "separator" => config('setup.purchase.number.separator', '/'),
        ];
    }
}
