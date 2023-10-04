<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
        $uploadResult = Cloudinary::upload($file->getRealPath(), [
            'folder' => 'videos',
            'resource_type' => 'video',
        ]);
    
        // Cloudinary public ID
        $publicId = $uploadResult->getPublicId();
        $fileLink = $uploadResult->getSecurePath();
    
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
            
            'file_size' => $file->getSize(),
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