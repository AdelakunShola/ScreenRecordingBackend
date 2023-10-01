<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Video;

class VideoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_create_video()
    {
        Storage::fake('videos');
    
        $file = UploadedFile::fake()->create('video.mp4', 100); // Adjust size as needed
    
        $response = $this->postJson('/api/videos', [
            'title' => $this->faker->sentence,
            'file' => $file,
            'thumbnail' => null, // Adjust as needed
            'description' => $this->faker->paragraph,
        ]);
    
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Video saved successfully',
            ]);
    
        // Check that the file was stored in the fake storage
        Storage::disk('videos')->assertExists($file->hashName());
    
        // Additional assertions as needed
        $this->assertDatabaseHas('videos', ['file_name' => $file->hashName()]);
    }
    

    public function test_can_get_all_videos()
    {
        $this->withoutExceptionHandling();

        // Create some sample videos
        Video::factory(3)->create();

        $response = $this->getJson('/api/videos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'file_name',
                    'file_link',
                    'thumbnail',
                    'description',
                    'file_size',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_can_get_single_video()
    {
        $this->withoutExceptionHandling();

        $video = Video::factory()->create();

        $response = $this->getJson('/api/videos/' . $video->id);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $video->id,
                'title' => $video->title,
                'file_name' => $video->file_name,
                // ... other fields ...
            ]);
    }

    public function test_can_delete_video()
    {
        $this->withoutExceptionHandling();

        $video = Video::factory()->create();

        $response = $this->deleteJson('/api/videos/' . $video->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Video deleted successfully',
            ]);

        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }
}
