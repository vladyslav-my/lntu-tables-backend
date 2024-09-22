<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\AuthResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UsersResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{


    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::where('name', 'LIKE', "%$search%")
            ->orWhere('last_name', 'LIKE', "%$search%")
            ->limit(5)
            ->get();

        return UsersResource::collection($users);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        
        $token = $user->createToken('token')->plainTextToken;
        
        return response()->json([
            'token' => $token,
        ], 200);
    }


    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'last_name' => $fields['last_name'],
            'phone_number' => $fields['phone_number'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'token' => $token,
        ];

        return response()->json($response, 201);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successfully',
        ], 200);
    }


    public function me(Request $request)
    {
        return [
            'id' => $request->user()->id,
        ];
    }


    public function checkToken(Request $request)
    {
        $token = $request->bearerToken();

        $tokenData = PersonalAccessToken::findToken($token);
        
        $isExpired = null;
        $isRevoked = null;

        if ($tokenData) {
            $isExpired = $tokenData->expires_at !== null && $tokenData->expires_at->isPast();
            $isRevoked = $tokenData->revoked;
        }

        $isValid = $tokenData && !$isExpired && !$isRevoked;

        return response()->json(
            [
                'is_valid' => $isValid,
            ], 200
        );
    }

    public function show(Request $request)
    {
        $user = $request->user();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'phone_number' => $user->phone_number,
            'role' => $user->role,
            'image' => $user->media('user_images')->first(),
            'email' => $user->email,
        ];
    }

    public function uploadPhoto(Request $request, User $user)
    {
        $file = $request->file('image');

        $userAuth = $request->user();

        $user = $user->findOrFail($userAuth->id);

        $user->addMedia($file)->toMediaCollection('user_images');

        return response()->json([
            'message' => 'Image uploaded successfully',
        ], 200);
    }
}
