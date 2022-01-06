<?php

namespace App\Http\Controllers;

use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $collection = Vendor::filter()->latest()->collective();

        return VendorResource::collection($collection);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "code" => "required|alpha_dash|unique:vendors",
            "name" => "required|string",
            "email" => "nullable|email",
            "phone" => "nullable|phone|min:10",
            "address" => "nullable|string",
        ]);

        $row = $request->only(['code', 'name', 'email', 'phone', 'address']);

        $record = Vendor::create($row);

        $message = "Vendor [$record->code] has been created";

        $record->createLog($message);

        return (new VendorResource($record))
            ->additional(["message" => $message]);
    }

    public function show($id)
    {
        $record = Vendor::findOrFail($id);

        return (new VendorResource($record));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            "code" => "required|alpha_dash|unique:vendors,code,$id,id",
            "name" => "required|string",
            "email" => "nullable|email",
            "phone" => "nullable|phone|min:10",
            "address" => "nullable|string",
        ]);

        $record = Vendor::findOrFail($id);

        $row = $request->only(['code', 'name', 'email', 'phone', 'address']);

        $record->update($row);

        $message = "Vendor [$record->code] has been updated";

        $record->createLog($message);

        return (new VendorResource($record))
            ->additional(["message" => $message]);
    }

    public function destroy($id)
    {
        $record = Vendor::findOrFail($id);

        $record->delete();

        $message = "Vendor [$record->code] has been deleted";

        $record->createLog($message);

        return response()->json(["message" => $message]);
    }
}
