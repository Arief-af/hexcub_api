<?php

namespace App\Http\Controllers;

use App\Models\Meet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MeetController extends Controller
{
    public function index()
    {
        $meets = Meet::first();

        return response()->json([
            'message' => 'Meets Retrieved Successfully',
            'data' => $meets
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required',
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'url' => 'required|string|max:255',
        ]);
        $countData = Meet::count();
        if ($countData > 0) {
            $firstData = Meet::first();
            $firstData->update($validated);
            return response()->json([
                'message' => 'Meet Updated Successfully',
                'data' => $firstData
            ], Response::HTTP_OK);
        } else {
            try {
                $meet = Meet::create($validated);
                return response()->json([
                    'message' => 'Meet Created Successfully',
                    'data' => $meet
                ], Response::HTTP_CREATED);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed to create meet',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function show(int $id)
    {
        $meet = Meet::find($id);
        if (!$meet) {
            return response()->json([
                'message' => 'Meet not found'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'Meet Retrieved Successfully',
            'data' => $meet
        ], Response::HTTP_OK);
    }

    public function update(Request $request, int $id)
    {
        $meet = Meet::find($id);
        if (!$meet) {
            return response()->json([
                'message' => 'Meet not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'url' => 'required|string|max:255',
        ]);

        $meet->update($validated);
        return response()->json([
            'message' => 'Meet Updated Successfully',
            'data' => $meet
        ], Response::HTTP_OK);
    }

    public function destroy(int $id)
    {
        $meet = Meet::find($id);
        if (!$meet) {
            return response()->json([
                'message' => 'Meet not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $meet->delete();
        return response()->json([
            'message' => 'Meet Deleted Successfully'
        ], Response::HTTP_OK);
    }
}