<?php

namespace App\Traits;

use App\Models\Loggable;

trait HasLoggable
{
    public function logs()
    {
        return $this->morphMany(Loggable::class, 'model');
    }

    public function createLog(string $text)
    {
        $this->logs()->create(['text' => $text]);
    }
}
