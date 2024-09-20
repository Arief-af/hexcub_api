<?php

namespace App\Http\Controllers;

use App\Models\VideoDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoDetailController extends Controller
{
    public function index()
    {
        $videoDetails = VideoDetail::with('video')->latest()->paginate(10);

        return response()->json([
            'message' => 'Video Details Retrieved Successfully',
            'data' => $videoDetails
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'video_id' => 'required|exists:videos,id',
            'title' => 'required|string|max:255',
            'time' => 'required|date_format:H:i:s',
        ]);

        try {
            $videoDetail = VideoDetail::create($validated);
            return response()->json([
                'message' => 'Video Detail Created Successfully',
                'data' => $videoDetail
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create video detail',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        $videoDetail = VideoDetail::with('video')->find($id);
        if (!$videoDetail) {
            return response()->json([
                'message' => 'Video Detail not found'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'Video Detail Retrieved Successfully',
            'data' => $videoDetail
        ], Response::HTTP_OK);
    }

    public function update(Request $request, int $id)
    {
        $videoDetail = VideoDetail::find($id);
        if (!$videoDetail) {
            return response()->json([
                'message' => 'Video Detail not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'video_id' => 'required|exists:videos,id',
            'title' => 'required|string|max:255',
            'time' => 'required|date_format:H:i:s',
        ]);

        $videoDetail->update($validated);
        return response()->json([
            'message' => 'Video Detail Updated Successfully',
            'data' => $videoDetail
        ], Response::HTTP_OK);
    }

    public function destroy(int $id)
    {
        $videoDetail = VideoDetail::find($id);
        if (!$videoDetail) {
            return response()->json([
                'message' => 'Video Detail not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $videoDetail->delete();
        return response()->json([
            'message' => 'Video Detail Deleted Successfully'
        ], Response::HTTP_OK);
    }
}