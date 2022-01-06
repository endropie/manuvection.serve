<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseResource;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $collection = Purchase::filter()->latest()->collective();

        return PurchaseResource::collection($collection);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "vendor_id" => "required|exists:vendors,id",
            "number" => "nullable|unique:purchases,numeric",
            "date" => "required|date",
            "due" => "nullable|date",
            "description" => "nullable",
            "items" => "required|array|min:1",
            "items.*.name" => "required|string",
            "items.*.quantity" => "required|numeric|gt:0",
            "items.*.price" => "required|numeric|gt:0",
            "items.*.notes" => "nullable|string",
        ]);

        \DB::beginTransaction();

        $row = $request->only(["vendor_id", "number", "date", "due", "description"]);

        $record = Purchase::create($row);

        foreach ($request->get('items') as $rowItem) {
            $reqItem = new Request($rowItem);
            $rowData = $reqItem->only(['name', 'quantity', 'price', 'notes']);

            $record->items()->create($rowData);
        }

        $record->setNextNumber();

        $message = "Purchase [$record->number] has been created";

        $record->createLog($message);

        \DB::commit();

        return (new PurchaseResource($record))
            ->additional(["message" => $message]);
    }

    public function show($id)
    {
        $record = Purchase::findOrFail($id);

        return (new PurchaseResource($record));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            "vendor_id" => "nullable|exists:vendors,id",
            "number" => "nullable|unique:purchases,number",
            "date" => "required|date",
            "due" => "nullable|date",
            "description" => "nullable",
            "items" => "nullable|array",
            "items.*.name" => "required|string",
            "items.*.quantity" => "required|numeric|gt:0",
            "items.*.notes" => "nullable|string",
        ]);

        $record = Purchase::findOrFail($id);

        $row = $request->only(["vendor_id", "number", "date", "due", "description"]);

        $record->update($row);

        if ($request->has('items'))
        {
            foreach ($request->get('items') as $rowItem) {
                $reqItem = new Request($rowItem);
                $rowData = $reqItem->only(['name', 'quantity', 'price', 'notes']);

                $record->items()->updateOrCreate($rowData['id'] ?? null, $rowData);
            }
        }

        $message = "Purchase [$record->number] has been updated";

        $record->createLog($message);

        return (new PurchaseResource($record))
            ->additional(["message" => $message]);
    }

    public function destroy($id)
    {
        $record = Purchase::findOrFail($id);

        $record->items()->delete();
        $record->delete();

        $message = "Purchase [$record->number] has been deleted";

        $record->createLog($message);

        return response()->json(["message" => $message]);
    }
}
