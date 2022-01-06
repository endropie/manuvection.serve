<?php

namespace App\Models;

use App\Traits\HasLoggable;
use App\Traits\HasNextNumber;
use Endropie\ApiToolkit\Traits\HasFilterable;
use Illuminate\Database\Eloquent\Model;

class Receive extends Model
{
    use HasNextNumber, HasLoggable, HasFilterable;

    protected $fillable = ["vendor_id", "number", "date", "due", "description"];

    public function items()
    {
        return $this->hasMany(ReceiveItem::class);
    }

    public static function getConfigNextNumber()
    {
        return [
            "column" => 'number',
            "prefix" => config('setup.receive.number.prefix', 'ROC'),
            "period" => config('setup.receive.number.period', 'Y-m'),
            "digits" => config('setup.receive.number.digits', 5),
            "separator" => config('setup.receive.number.separator', '/'),
        ];
    }
}
