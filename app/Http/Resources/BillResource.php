<?php

namespace App\Http\Resources;

use Endropie\ApiToolkit\Http\Resource;

class BillResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            $this->mergeAttributes(),
        ];
    }
}
