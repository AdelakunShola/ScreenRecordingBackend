<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\File;
use getID3;
use GuzzleHttp\Client;

class VideoController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'title' => 'nullable|string',
            'file' => 'required|file|mimes:mp4,mov,avi',
            'thumbnail' => 'nullable|image',
            'description' => 'nullable|string',
        ]);

        // Handle file upload to Cloudinary
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

         // Open the file for reading
         $handle = fopen($file->getRealPath(), 'rb');

        // Determine chunk size (adjust as needed)
        $chunkSize = 1024 * 1024; // 1 MB chunks

        // Initialize Cloudinary public ID and file link
        $publicId = null;
        $fileLink = null;

        // Initialize $transcription variable
        $transcription = null;

        try {
             // Loop through chunks
             while (!feof($handle)) {
                // Read a chunk of data
                $chunk = fread($handle, $chunkSize);

                // Upload the chunk to Cloudinary
                $uploadResult = Cloudinary::upload('data://text/plain;base64,' . base64_encode($chunk), [
                    'folder' => 'videos',
                    'resource_type' => 'video',
                ]);

                // Update Cloudinary public ID and file link
                $publicId = $uploadResult->getPublicId();
                $fileLink = $uploadResult->getSecurePath();
             }

         
        } finally {
            // Close the file handle
            fclose($handle);
        }

        // Handle optional thumbnail upload to Cloudinary
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailUploadResult = Cloudinary::upload($thumbnail->getRealPath(), [
                'folder' => 'thumbnails',
                'resource_type' => 'image',
            ]);
            $thumbnailPath = $thumbnailUploadResult->getSecurePath();
        }

        // Create video record
        $video = Video::create([
            'title' => $request->input('title'),
            'file_name' => $publicId, // Store Cloudinary public ID
            'file_link' => $fileLink,
            'thumbnail' => $thumbnailPath,
            'description' => $request->input('description'),
            'file_size' => $fileSize,
            'transcription' => $transcription['transcription'] ?? null,  // Assuming the API response has a 'transcription' key
        ]);

        // Convert file size to megabytes
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);

        // Update the 'file_size' field to the size in megabytes
        $video['file_size'] = $fileSizeMB . ' MB';

        // Get the duration of the video using getID3
        $videoPath = public_path('videos/' . $publicId . '.mp4');
        $getID3 = new \getID3();
        $fileInfo = $getID3->analyze($videoPath);

        // Check if duration information is available
        if (isset($fileInfo['playtime_seconds'])) {
            $duration = $fileInfo['playtime_seconds'];

            // Convert seconds to HH:MM:SS format
            $formattedDuration = gmdate("H:i:s", $duration);

            // Update the 'length' field in the video
            $video['length'] = $formattedDuration;
        } else {
            // Handle the case where duration information is not available
            $video['length'] = 'N/A';
        }

        // Assuming $fileLink contains the URL of the uploaded file on Cloudinary
        $whisperApiUrl = 'https://transcribe.whisperapi.com';  // Replace with the actual WhisperAPI endpoint
        $apiKey = 'N7G2M3UC7Z4JJYJY3VJ9RCEIVLQZ8UBT';  // Replace with the actual API key provided by WhisperAPI

        // Make a request to WhisperAPI using Guzzle
        $client = new Client();
        $response = $client->post($whisperApiUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ],
            'json' => [
                'url' => $fileLink,
            ],
        ]);

        // Check if the request was successful (status code 200)
        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody(), true);

            // Check if decoding was successful and the 'transcription' key exists
            if ($decodedResponse !== null && isset($decodedResponse['transcription'])) {
                $transcription = $decodedResponse['transcription'];
            } else {
                // Handle the case where the 'transcription' key is missing in the response
                return response()->json([
                    'error' => 'Transcription key missing in WhisperAPI response',
                ], 500);
            }

            return response()->json([
                'message' => 'Video uploaded and transcribed successfully',
                'video' => $video,
                'transcription' => $transcription,
            ], 201);
        } else {
            // If the request was not successful, handle the error
    $errorDetails = json_decode($response->getBody(), true);
    $errorMessage = isset($errorDetails['error']) ? $errorDetails['error'] : 'Failed to transcribe the video';

            
    return response()->json([
        'error' => $errorMessage,
        'details' => $errorDetails,  // You can log or inspect the response body for details
    ], 500);
        }
    }

   


    public function index()
    {
        $videos = Video::all();
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