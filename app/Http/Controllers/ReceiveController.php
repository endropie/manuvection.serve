<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReceiveResource;
use App\Models\Receive;
use Illuminate\Http\Request;

class ReceiveController extends Controller
{
    public function index()
    {
        $collection = Receive::filter()->latest()->collective();

        return ReceiveResource::collection($collection);
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            "vendor_id" => "required|exists:vendors,id",
            "number" => "nullable|unique:purchases,numeric",
            "date" => "required|date",
            "description" => "nullable",
            "items" => "required|array|min:1",
            "items.*.quantity" => "required|numeric|gt:0",
            "items.*.notes" => "nullable|string",
            "items.*.purchase_item_id" => "required|exists:purchase_items,id",
        ]);

        \DB::beginTransaction();

        $row = $request->only(["vendor_id", "number", "date", "due", "description"]);

        $record = Receive::create($row);

        foreach ($request->get('items') as $rowKey => $rowItem) {
            $reqItem = new Request($rowItem);
            $rowData = $reqItem->only(['name', 'quantity', 'notes']);
            $recordItem = $record->items()->create($rowData);

            $saveBaseReceive = $this->savePurchaseItem($reqItem, $recordItem, $rowKey);

            if (is_object($saveBaseReceive) && get_class($saveBaseReceive) == \Illuminate\Http\JsonResponse::class) {
                return dd($saveBaseReceive);
            }
        }

        $record->setNextNumber();

        $message = "Receive [$record->number] has been created";

        $record->createLog($message);

        \DB::commit();

        return (new ReceiveResource($record))
            ->additional(["message" => $message]);
    }

    public function show($id)
    {
        $record = Receive::findOrFail($id);

        return (new ReceiveResource($record));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            "vendor_id" => "nullable|exists:vendors,id",
            "number" => "nullable|unique:purchases,number",
            "date" => "nullable|date",
            "description" => "nullable",
            "items" => "nullable|array",
            "items.*.id" => "nullable|exists:receive_items,id",
            "items.*.quantity" => "nullable|numeric|gt:0",
            "items.*.notes" => "nullable|string",
            "items.*.purchase_item_id" => "nullable|exists:purchase_items,id",
        ]);

        $record = Receive::findOrFail($id);

        $row = $request->only(["vendor_id", "number", "date", "description"]);

        $record->update($row);

        if ($request->has('items'))
        {
            foreach ($request->get('items') as $rowKey => $rowItem) {
                if (isset($rowItem['_delete']))
                {
                    if ($recordItem = $record->items()->find($rowItem['id']))
                    {
                        $recordItem->delete();
                    }
                    continue;
                }

                $reqItem = new Request($rowItem);
                $rowData = $reqItem->only(['quantity', 'notes']);

                $recordItem = $record->items()->updateOrCreate($rowData['id'], $rowData);

                $saveBaseReceive = $this->savePurchaseItem($reqItem, $recordItem, $rowKey);

                if (is_object($saveBaseReceive) && get_class($saveBaseReceive) == \Illuminate\Http\JsonResponse::class) {
                    return ($saveBaseReceive);
                }
            }
        }

        $message = "Receive [$record->number] has been updated";

        $record->createLog($message);

        return (new ReceiveResource($record))
            ->additional(["message" => $message]);
    }

    public function destroy($id)
    {
        $record = Receive::findOrFail($id);

        $record->items()->delete();

        $record->delete();

        $message = "Receive [$record->number] has been deleted";

        $record->createLog($message);

        return response()->json(["message" => $message]);
    }

    protected function savePurchaseItem($request, $receiveItem, $key)
    {
        if ($request->has("purchase_item_id"))
        {
            $purchaseItem = app(\App\Models\PurchaseItem::class)->find($request->get("purchase_item_id"));

            if ($purchaseItem && $purchaseItem->purchase->vendor_id === $receiveItem->receive->vendor_id)
            {
                $purchaseItem->receive_items()->save($receiveItem);
            }
            else {
                return $this->buildFailedValidationResponse($request, [
                    "items.$key.purchase_item_id" => ["The purchase reference invalid."],
                ]);
            }
        }
    }
}
