<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Notifications\VerifyEmail;

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
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images'), $imageName);
                $validated['image'] = 'images/' . $imageName;
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

    public function update(Request $request)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Validate request data
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255',
            'phone_number' => 'string|max:15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Hash password if provided
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Handle image upload and deletion
        if ($request->hasFile('image')) {
            if ($user->image && file_exists(public_path($user->image))) {
                unlink(public_path($user->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validated['image'] = 'images/' . $imageName;
        }

        $emailUpdated = false;

        $oldEmail = $user->email;
        // Check if email has changed
        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            // Ensure the new email is unique
            if (User::where('email', $validated['email'])->exists()) {
                return response()->json([
                    'message' => 'Email already exists'
                ], Response::HTTP_CONFLICT);
            }

            // Set email_verified_at to null and notify user to verify email
            $validated['email_verified_at'] = null;
            $emailUpdated = true;
        }
        
        // Update the user
        $user->update($validated);
        if ($oldEmail !== $request->email) {
            $user->notify(new VerifyEmail());
        }

        // Return different responses based on whether the email was updated
        if ($emailUpdated) {
            return response()->json([
                'message' => 'Update berhasil, silakan cek email Anda untuk verifikasi.',
                'data' => $user
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'User updated successfully.',
                'data' => $user
            ], Response::HTTP_OK);
        }
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
