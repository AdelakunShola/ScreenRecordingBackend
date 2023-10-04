<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function store(Request $request)
    {
     // Validate the request
     $request->validate([
        'file' => 'required|mimes:mp4|max:102400',
        'thumbnail' => 'nullable|image',
    ]);

    // Handle file upload to Cloudinary
    $file = $request->file('file');
    $chunkSize = 1024 * 1024; // 1 MB chunks

    // Generate a unique identifier for the file
    $uniqueIdentifier = uniqid();

    // Store the file on the local disk using Laravel Storage
    Storage::disk('local')->put('temp/' . $uniqueIdentifier, $file->get());

    // Get the total size of the file
    $totalSize = Storage::disk('local')->size('temp/' . $uniqueIdentifier);

    // Initialize Cloudinary public ID and file link 
    $publicId = null;
    $fileLink = null;

    try {
        // Loop through chunks
        for ($i = 0; $i < ceil($totalSize / $chunkSize); $i++) {
            // Read a chunk of data
            $chunk = Storage::disk('local')->read('temp/' . $uniqueIdentifier, $i * $chunkSize, $chunkSize);

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
      
        Storage::disk('local')->delete('temp/' . $uniqueIdentifier);
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
        'file_name' => $publicId, // Store Cloudinary public ID
        'file_link' => $fileLink,
        'thumbnail' => $thumbnailPath,
        'file_size' => $totalSize,
    ]);

    return response()->json($video, 201);
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