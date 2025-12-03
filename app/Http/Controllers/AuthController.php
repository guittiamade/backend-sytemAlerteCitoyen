<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        if (!$request->filled('email')) {
            $request->merge(['email' => null]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'tel' => ['required', 'string', 'max:30', 'unique:users,tel'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['profile_id'] = Profile::where('nom', 'citoyen')->value('id');

        $user = User::create($validated);
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie. Votre compte citoyen est prêt.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'tel' => ['required', 'string', 'max:30'],
            'password' => ['required'],
        ]);

        $user = User::where('tel', $credentials['tel'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 422);
        }

        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function me(Request $request)
    {
        return $request->user()->load(['profile','direction']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté']);
    }

    public function updateMe(Request $request)
    {
        $user = $request->user();
        if ($request->has('email') && !$request->filled('email')) {
            $request->merge(['email' => null]);
        }
        $validated = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'tel' => ['sometimes','string','max:30','unique:users,tel,' . $user->id],
            'email' => ['sometimes','nullable','email','max:255','unique:users,email,' . $user->id],
            'password' => ['sometimes', Password::defaults()],
        ]);
        if (array_key_exists('password', $validated)) {
            $validated['password'] = Hash::make($validated['password']);
        }
        $user->update($validated);
        return $user->fresh()->load(['profile','direction']);
    }
}


