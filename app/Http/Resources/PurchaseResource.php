<?php

namespace App\Http\Resources;

use Endropie\ApiToolkit\Http\Resource;

class PurchaseResource extends Resource
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
            $this->mergeInclude('items', function() {
                return PurchaseResource::collection($this->resource->items);
            }),
            $this->mergeInclude('vendor', function() {
                return new VendorResource($this->resource->vendor);
            }),
            $this->mergeField('amount', function() {
                return (double) $this->resource->amount;
            }),
        ];
    }
}
