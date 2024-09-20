<?php

namespace App\Http\Controllers;

use App\Models\VideoComment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoCommentController extends Controller
{
    public function index()
    {
        $comments = VideoComment::with('user')->latest()->paginate(10);

        return response()->json([
            'message' => 'Video Comments Retrieved Successfully',
            'data' => $comments
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        try {
            $comment = VideoComment::create($validated);
            return response()->json([
                'message' => 'Video Comment Created Successfully',
                'data' => $comment
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create video comment',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        $comment = VideoComment::with('user')->find($id);
        if (!$comment) {
            return response()->json([
                'message' => 'Video Comment not found'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'Video Comment Retrieved Successfully',
            'data' => $comment
        ], Response::HTTP_OK);
    }

    public function update(Request $request, int $id)
    {
        $comment = VideoComment::find($id);
        if (!$comment) {
            return response()->json([
                'message' => 'Video Comment not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'message' => 'nullable|string',
        ]);

        $comment->update($validated);
        return response()->json([
            'message' => 'Video Comment Updated Successfully',
            'data' => $comment
        ], Response::HTTP_OK);
    }

    public function destroy(int $id)
    {
        $comment = VideoComment::find($id);
        if (!$comment) {
            return response()->json([
                'message' => 'Video Comment not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $comment->delete();
        return response()->json([
            'message' => 'Video Comment Deleted Successfully'
        ], Response::HTTP_OK);
    }
}