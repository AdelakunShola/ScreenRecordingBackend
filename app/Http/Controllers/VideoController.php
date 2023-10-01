<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use getID3;
use GuzzleHttp\Client;
use DateTime;

class VideoController extends Controller
{
    public function store(Request $request)
    {
         // Validate the request
         $request->validate([
           
            'file' => 'required|file|mimes:mp4,mov,avi',
            'thumbnail' => 'nullable|image',
           
        ]);

        // Handle file upload to local storage
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        // Upload video to local storage (assuming 'videos' is the disk name)
        $path = $file->storeAs('videos', $fileName, 'videos');  // Use storeAs to set a specific filename
        $fileLink = Storage::url($path);

        // Handle optional thumbnail upload to local storage
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailPath = $thumbnail->store('thumbnails', 'thumbnails');
        }

        // Create video record
        $video = Video::create([
            'id' => Video::max('id') + 1,
            'file_name' => $fileName,
            'file_link' => asset(Storage::url($path)),
            'thumbnail' => $thumbnailPath,
            'file_size' => $fileSize,
        ]);

        // Convert file size to megabytes
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);

        // Update the 'file_size' field to the size in megabytes
        $video['file_size'] = $fileSizeMB . ' MB';

        // Get the duration of the video using getID3
        $videoPath = storage_path('app/public/videos/' . $fileName);
        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($videoPath);
        

        // Check if duration information is available
        if (isset($fileInfo['playtime_seconds'])) {
            $duration = $fileInfo['playtime_seconds'];

            // Convert seconds to HH:MM:SS format
            $formattedDuration = gmdate("H:i:s", $duration);

            // Update the 'length' field in the video
            $video->length = $formattedDuration;
        } else {
            // Handle the case where duration information is not available
            $video['length'] = 'N/A';
        }

        // Format the uploaded time in the desired format
    $uploadedTime = new DateTime($video->uploaded_time);
    $formattedUploadedTime = $uploadedTime->format('j, F Y');

    $video->uploaded_time = $formattedUploadedTime;


        return response()->json([
            'message' => 'Video uploaded successfully',
            'video' => $video,
        ], 201);
    }
   


    public function index()
    {
        $videos = Video::all();

        // For each video, retrieve the duration and include it in the response
foreach ($videos as $video) {
    // You can format the duration as needed
    $video->duration = $video->length ?? 'N/A';
}
        return response()->json($videos);
    }

    public function show($id)
    {
        $video = Video::findOrFail($id);
        return response()->json($video);
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();
        return response()->json(['message' => 'Video deleted successfully']);
    }
}