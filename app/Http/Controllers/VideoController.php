<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->paginate(10);

        return response()->json([
            'message' => 'Videos Retrieved Successfully',
            'data' => $videos
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|string|max:255',
            'file' => 'required|string|max:255',
        ]);

        try {
            $video = Video::create($validated);
            return response()->json([
                'message' => 'Video Created Successfully',
                'data' => $video
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create video',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json([
                'message' => 'Video not found'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'Video Retrieved Successfully',
            'data' => $video
        ], Response::HTTP_OK);
    }

    public function update(Request $request, int $id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json([
                'message' => 'Video not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|string|max:255',
            'file' => 'required|string|max:255',
        ]);

        $video->update($validated);
        return response()->json([
            'message' => 'Video Updated Successfully',
            'data' => $video
        ], Response::HTTP_OK);
    }

    public function destroy(int $id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json([
                'message' => 'Video not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $video->delete();
        return response()->json([
            'message' => 'Video Deleted Successfully'
        ], Response::HTTP_OK);
    }
}