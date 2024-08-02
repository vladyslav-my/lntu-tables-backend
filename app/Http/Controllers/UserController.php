<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\AuthResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{


    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::where('name', 'LIKE', "%$search%")
            ->orWhere('last_name', 'LIKE', "%$search%")
            ->limit(5)
            ->get();

        return UserResource::collection($users);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $user = User::where('email', $request->email)->first();

        if (!$user && !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return AuthResource::make($user);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        return AuthResource::make($user);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
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
