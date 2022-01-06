<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => ["required", "email", "unique:users,email"],
            'mobile' => ["nullable", "unique:users,mobile", 'phone'],
            'name' => ["nullable", 'string', 'min:6', 'alpha_dash'],
            'password' => ["required", "min:6"],
        ]);

        // Create new user
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->password = app('hash')->make($request->password);

            if ($user->save()) {
                $request->merge(['username' => $request->mobile ?? $request->email]);
                return $this->login($request);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $mode = filter_var($request->get('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $this->validate($request, [
            'username' => ["required", "exists:users,$mode"],
            'password' => ["required"],
        ]);

        $request->merge(["$mode" => $request->username]);

        $credentials = request([$mode, 'password']);

        $ttl = auth()->factory()->getTTL() * 60 * 24 * ($request->remember ? 30 : 1);

        if (!$token = auth()->setTTL($ttl)->attempt($credentials)) {
            return response()->json(['message' => "Username & password doesn't match"], 401);
        }

        return $this->respondWithToken($token, $ttl);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user()
    {
        $user = auth()->user();
        $response  = new UserResource($user);
        $response->default(['email', 'mobile', 'ability']);
        return $response;
    }

    public function passwordChange(Request $request)
    {
        $this->validate($request, [
            'password' => ["required"],
            'new_password' => ["required", "min:6", "not_in:$request->password"],
            'new_password_confirm' => ["required", "same:new_password",],
        ]);

        $user = auth()->user();
        if (app('hash')->check($request->password, $user->password)) {
            $user->password = app('hash')->make($request->get('new_password'));
            $user->save();

            return [
                'message' => 'Password change success.'
            ];
        }

        return response()->json([
            'message' => 'Password change failed.'
        ], 400);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $ttl)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl,
        ]);
    }
}
