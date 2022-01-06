<?php

namespace App\Http\Controllers;

use App\Http\Resources\BillResource;
use App\Models\Bill;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index()
    {
        $collection = Bill::filter()->latest()->collective();

        return BillResource::collection($collection);
    }

    public function show($id)
    {
        $record = Bill::findOrFail($id);

        return (new BillResource($record));
    }

    public function storeBaseReceive($id, Request $request)
    {
        $receive = \App\Models\Receive::findOrFail($id);

        $row = $request->only(["vendor_id", "number", "date", "due", "description"]);

        $record = Bill::create([
            "vendor_id" => $receive->vendor_id,
            "date" => $receive->date,
            "description" => "Reference: create base on Receive[$receive->id]",
        ]);

        foreach ($receive->items as $receiveItemKey => $receiveItem) {

            $reqItem = new Request($receiveItem->toArray());
            $rowData = $reqItem->only(['quantity', 'notes']);

            $recordItem = $record->items()->create($rowData);

            $reqItem->merge(['base_type' => get_class($receiveItem), 'base_id' => $receiveItem->id]);

            $saveBaseBill = $this->saveBaseItem($reqItem, $recordItem, $receiveItemKey);

            if (is_object($saveBaseBill) && get_class($saveBaseBill) == \Illuminate\Http\JsonResponse::class) {
                return ($saveBaseBill);
            }
        }

        $message = "Bill [$record->number] has been updated";

        $record->createLog($message);

        return (new BillResource($record))
            ->additional(["message" => $message]);

    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            "vendor_id" => "nullable|exists:vendors,id",
            "number" => "nullable|unique:purchases,number",
            "date" => "nullable|date",
            "due" => "nullable|date",
            "description" => "nullable",
            "items" => "nullable|array",
            "items.*.id" => "nullable|exists:receive_items,id",
            "items.*.quantity" => "nullable|numeric|gt:0",
            "items.*.notes" => "nullable|string",
            "items.*.purchase_item_id" => "nullable|exists:purchase_items,id",
        ]);

        $record = Bill::findOrFail($id);

        $row = $request->only(["vendor_id", "number", "date", "due", "description"]);

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

                $saveBaseBill = $this->saveBaseItem($reqItem, $recordItem, $rowKey);

                if (is_object($saveBaseBill) && get_class($saveBaseBill) == \Illuminate\Http\JsonResponse::class) {
                    return ($saveBaseBill);
                }
            }
        }

        $message = "Bill [$record->number] has been updated";

        $record->createLog($message);

        return (new BillResource($record))
            ->additional(["message" => $message]);
    }

    public function destroy($id)
    {
        $record = Bill::findOrFail($id);

        $record->items()->delete();

        $record->delete();

        $message = "Bill [$record->number] has been deleted";

        $record->createLog($message);

        return response()->json(["message" => $message]);
    }

    protected function saveBaseItem($request, $billItem, $key)
    {
        if ($request->has("purchase_item_id"))
        {
            $baseItem = app($request->get("base_type"))->find($request->get("base_id"));

            if ($baseItem) {
                $billItem->base()->save($baseItem);
            }
            else {
                return $this->buildFailedValidationResponse($request, [
                    "items.$key.base_id" => ["The base reference invalid."],
                    "items.$key.base_type" => ["The base reference invalid."],
                ]);
            }
        }
    }
}
