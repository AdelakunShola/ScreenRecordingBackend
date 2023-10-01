<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'size' => $this->size,
            'length' => $this->length,
            'path' => $this->path,
            'uploaded_time' => $this->uploaded_time,
            // Add more fields as needed
        ];
    }
}
