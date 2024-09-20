<?php

namespace App\Http\Controllers;

use App\Models\UserVideo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserVideoController extends Controller
{
    public function index()
    {
        $userVideos = UserVideo::with(['user', 'video'])->latest()->paginate(10);

        return response()->json([
            'message' => 'User Videos Retrieved Successfully',
            'data' => $userVideos
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'video_id' => 'required|exists:videos,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string',
        ]);

        try {
            $userVideo = UserVideo::create($validated);
            return response()->json([
                'message' => 'User Video Created Successfully',
                'data' => $userVideo
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user video',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        $userVideo = UserVideo::with(['user', 'video'])->find($id);
        if (!$userVideo) {
            return response()->json([
                'message' => 'User Video not found'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'User Video Retrieved Successfully',
            'data' => $userVideo
        ], Response::HTTP_OK);
    }

    public function update(Request $request, int $id)
    {
        $userVideo = UserVideo::find($id);
        if (!$userVideo) {
            return response()->json([
                'message' => 'User Video not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'status' => 'nullable|string',
        ]);

        $userVideo->update($validated);
        return response()->json([
            'message' => 'User Video Updated Successfully',
            'data' => $userVideo
        ], Response::HTTP_OK);
    }

    public function destroy(int $id)
    {
        $userVideo = UserVideo::find($id);
        if (!$userVideo) {
            return response()->json([
                'message' => 'User Video not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $userVideo->delete();
        return response()->json([
            'message' => 'User Video Deleted Successfully'
        ], Response::HTTP_OK);
    }
}