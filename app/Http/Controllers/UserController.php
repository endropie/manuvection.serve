<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $collection = User::filter()->latest()->collective();

        return UserResource::collection($collection);
    }

    public function show($id)
    {
        $record = User::findOrFail($id);

        return (new UserResource($record));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'email' => "required|email|unique:users,email,$id,id",
            'mobile' => "nullable|phone|unique:users,mobile,$id,id",
            'name' => "nullable|string|min:6|alpha_dash|unique:users,name,$id,id",
            'ability' => "nullable|array",
            'new_password' => "nullable|min:8",
            'new_password_confirm' => "required_with:new_password|same:new_password",
        ]);

        $record = User::findOrFail($id);

        if ($record->id != 1) {
            $row = $request->only(['email', 'mobile', 'name', 'ability']);
            $record->update($row);
        }

        if ($request->has('new_password')) {
            $record->password = app('hash')->make($request->password);
            $record->save();
        }

        $message = "User [" . ($record->name ?? $record->email) . "] has been updated";

        $record->createLog($message);

        return (new UserResource($record))
            ->additional(["message" => $message]);
    }

    public function destroy($id)
    {
        $record = User::findOrFail($id);

        $hasLoggable = \App\Models\Loggable::where('user_id', $record->id)->count();

        if ($record->id == 1 || $hasLoggable) {
            $message = "User [" . ($record->name ?? $record->email) . "] can not deleted";
            abort(501, $message);
        }

        $record->delete();

        $message = "User [" . ($record->name ?? $record->email) . "] has been updated";

        $record->createLog($message);

        return response()->json(["message" => $message]);
    }

    public function abilities()
    {
        return response()->json(['data' => config('auth.gates', [])]);
    }
}
