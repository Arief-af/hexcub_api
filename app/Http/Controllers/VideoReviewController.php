<?php

namespace App\Http\Controllers;

use App\Models\VideoReview;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoReviewController extends Controller
{
    public function index()
    {
        $reviews = VideoReview::with(['user', 'video'])->latest()->paginate(10);

        return response()->json([
            'message' => 'Video Reviews Retrieved Successfully',
            'data' => $reviews
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'video_id' => 'required|exists:videos,id',
            'rating' => 'required|integer|min:1|max:5',
            'description' => 'required|string',
        ]);

        try {
            $review = VideoReview::create($validated);
            return response()->json([
                'message' => 'Video Review Created Successfully',
                'data' => $review
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create video review',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        $review = VideoReview::with(['user', 'video'])->find($id);
        if (!$review) {
            return response()->json([
                'message' => 'Video Review not found'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'Video Review Retrieved Successfully',
            'data' => $review
        ], Response::HTTP_OK);
    }

    public function update(Request $request, int $id)
    {
        $review = VideoReview::find($id);
        if (!$review) {
            return response()->json([
                'message' => 'Video Review not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'description' => 'nullable|string',
        ]);

        $review->update($validated);
        return response()->json([
            'message' => 'Video Review Updated Successfully',
            'data' => $review
        ], Response::HTTP_OK);
    }

    public function destroy(int $id)
    {
        $review = VideoReview::find($id);
        if (!$review) {
            return response()->json([
                'message' => 'Video Review not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $review->delete();
        return response()->json([
            'message' => 'Video Review Deleted Successfully'
        ], Response::HTTP_OK);
    }
}