<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);

        return response()->json([
            'message' => 'Users Retrieved Successfully',
            'data' => $users
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            if ($request->hasFile('image')) {
                $imageName = time().'.'.$request->image->extension();  
                $request->image->move(public_path('images'), $imageName);
                $validated['image'] = 'images/'.$imageName;
            }

            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);
            return response()->json([
                'message' => 'User Created Successfully',
                'data' => $user
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'User Retrieved Successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function update(Request $request, int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'string|max:15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('image')) {
            if ($user->image && file_exists(public_path($user->image))) {
                unlink(public_path($user->image));
            }

            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);

            $validated['image'] = 'images/'.$imageName;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User Updated Successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }


    public function destroy(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $user->delete();
        return response()->json([
            'message' => 'User Deleted Successfully'
        ], Response::HTTP_OK);
    }
}