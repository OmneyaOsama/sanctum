<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'phone_number' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'phone_number' => $validatedData['phone_number'],
            'password' => bcrypt($validatedData['password']),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validatedData)) {
            throw ValidationException::withMessages([
                'phone_number' => 'Invalid credentials',
            ]);
        }

        $user = $request->user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ]);
    }
}
