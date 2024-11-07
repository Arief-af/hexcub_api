<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Str;
class AuthenticationController extends BaseController
{
    /**
     * Register API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15',
            'image' => 'nullable|string|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }        

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $input['image'] = 'images/' . $imageName;
        }

        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        $user->notify(new VerifyEmail());

        return $this->sendResponse($success, 'User registered successfully.');
    }

    /**
     * Login API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;

            return $this->sendResponse($success, 'User logged in successfully.');
        } else {
            return $this->sendError('Unauthorized', ['error' => 'Unauthorized'], 401);
        }
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'hash' => 'required|string',
        ]);
    
        $email = $request->input('email');
        $hash = $request->input('hash');
    
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return response()->json(['message' => 'Invalid verification token.'], 400);
        }
    
        if (sha1($user->email) !== $hash) { 
            return response()->json(['message' => 'Invalid verification token.'], 400);
        }
    
 
        $user->email_verified_at = now(); 
        $user->save();
    
        return response()->json(['message' => 'Email successfully verified.'], 200);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found.'], 404);
        }

        $token = Str::random(60);

        $user->notify(new ResetPasswordNotification($token));

        return response()->json(['message' => 'Reset password email sent.']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
    
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL) || strlen($request->token) !== 60) {
            return response()->json(['message' => 'Invalid token.'], 400);
        }
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json(['message' => 'Email not found.'], 404);
        }
    
        $user->password = Hash::make($request->password);
        $user->save();
    
        return response()->json(['message' => 'Password has been reset successfully.']);
    }
}