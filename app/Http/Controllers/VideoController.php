<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        // Check if a search query is provided
        $search = $request->query('search');

        // Query videos, filtering by title if a search query is provided, and include the 'videoTimestamps' relation
        $videos = Video::with('videoDetails')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', '%' . $search . '%');
            })
            ->oldest()
            ->paginate(10);

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
            'file' => 'required|mimes:mp4',
            'materi' => 'required|string',
            'timeStamps' => 'required|array',
            'timeStamps.*.title' => 'required|string',
            'timeStamps.*.time' => 'required|numeric'
        ]);

        try {
            // Upload file ke S3
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('videos', 's3');

                if (!$path) {
                    throw new \Exception('File gagal disimpan ke S3.');
                }

                $url = Storage::disk('s3')->url($path);
                $validated['file'] = $url;
            }

            // Simpan ke database
            $video = Video::create($validated);

            foreach ($request->input('timeStamps') as $timeStamp) {
                VideoDetail::create([
                    'video_id' => $video->id,
                    'time' => $timeStamp['time'],
                    'title' => $timeStamp['title']
                ]);
            }

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



    public function show($id)
    {
        try {
            // Find the video by ID
            $video = Video::with('videoDetails')->find($id); // Assuming 'details' is the relationship for VideoDetail

            if (!$video) {
                return response()->json([
                    'message' => 'Video not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Video Retrieved Successfully',
                'data' => $video
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve video',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            'materi' => 'required|string',
            'duration' => 'required|string|max:255',
        ]);

        try {
            if ($request->hasFile('file')) {
                // Upload file baru ke S3
                $file = $request->file('file');
                $path = $file->store('videos', 's3'); // Simpan di folder 'videos' dalam bucket
                $url = Storage::disk('s3')->url($path);
                $validated['file'] = $url;

                // Hapus file lama di S3 (jika ada)
                if ($video->file) {
                    $oldPath = ltrim(parse_url($video->file, PHP_URL_PATH), '/');
                    Storage::disk('s3')->delete($oldPath);
                }
            }

            $video->update($validated);
            $videoDetails = $request->input('video_details', []);
            if (is_array($videoDetails)) {
                foreach ($videoDetails as $timeStamp) {
                    if (isset($timeStamp['id'])) {
                        $detail = VideoDetail::find($timeStamp['id']);
                        if ($detail) {
                            $detail->update($timeStamp);
                        }
                    } else {
                        VideoDetail::create([
                            'video_id' => $video->id,
                            'time' => $timeStamp['time'],
                            'title' => $timeStamp['title']
                        ]);
                    }
                }
            }

            return response()->json([
                'message' => 'Video Updated Successfully',
                'data' => $video
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update video',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function destroy(int $id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json([
                'message' => 'Video not found'
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            // Delete the file associated with the video, if it exists
            if ($video->file) {
                $filePath = ltrim(parse_url($video->file, PHP_URL_PATH), '/');
                Storage::disk('s3')->delete($filePath);
            }
            $video->delete();

            return response()->json([
                'message' => 'Video Deleted Successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete video',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // public function stream(Request $request, $filename)
    // {
    //     $filePath = public_path('files/' . $filename); // Adjust path if 
    //     if (!file_exists($filePath)) {
    //         abort(404);
    //     }

    //     $fileSize = filesize($filePath);
    //     $start = 0;
    //     $end = $fileSize - 1;

    //     // Check if there's a Range header in the request
    //     if ($request->hasHeader('Range')) {
    //         $range = $request->header('Range');
    //         [$unit, $range] = explode('=', $range, 2);
    //         [$start, $end] = explode('-', $range);
    //         $start = intval($start);

    //         // If end is empty, it means request till the end of the file
    //         $end = ($end === '') ? $fileSize - 1 : intval($end);

    //         if ($end >= $fileSize) {
    //             $end = $fileSize - 1;
    //         }

    //         $length = $end - $start + 1;

    //         // Set the status to 206 Partial Content
    //         $headers = [
    //             'Content-Type' => 'video/mp4',
    //             'Content-Length' => $length,
    //             'Content-Range' => "bytes $start-$end/$fileSize",
    //             'Accept-Ranges' => 'bytes',
    //         ];

    //         $response = new StreamedResponse(function () use ($filePath, $start, $end) {
    //             $handle = fopen($filePath, 'rb');
    //             fseek($handle, $start);
    //             $buffer = 8192;
    //             while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
    //                 if ($pos + $buffer > $end) {
    //                     $buffer = $end - $pos + 1;
    //                 }
    //                 echo fread($handle, $buffer);
    //                 flush();
    //             }
    //             fclose($handle);
    //         }, 206, $headers);
    //     } else {
    //         // No Range header present
    //         $headers = [
    //             'Content-Type' => 'video/mp4',
    //             'Content-Length' => $fileSize,
    //             'Accept-Ranges' => 'bytes',
    //         ];

    //         $response = new StreamedResponse(function () use ($filePath) {
    //             readfile($filePath);
    //         }, 200, $headers);
    //     }

    //     return $response;
    // }

    public function stream(Request $request, $filename)
    {
        $disk = \Storage::disk('s3');
        $path = "videos/" . $filename;

        if (!$disk->exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $stream = $disk->readStream($path);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            "Content-Type" => "video/mp4",
            "Accept-Ranges" => "bytes",
        ]);
    }
}
