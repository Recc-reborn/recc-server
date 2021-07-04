<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Creates a new token for current session.
     *
     * From the docs:
     *
     * When the mobile application uses the token to make an API
     * request to your application, it should pass the token in
     * the Authorization header as a Bearer token.
     */
    public function requestToken(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($request->device_name)->plainTextToken;
    }

    /**
     * Revokes user's current access token
     */
    public function unauthenticate(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->currentAccessToken()->delete();
        } else {
            return response('Unauthenticated', 401);
        }
    }
}
