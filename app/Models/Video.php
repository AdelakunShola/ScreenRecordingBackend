<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'file_name', 'file_link', 'thumbnail', 'duration', 'description', 'file_size', 'transcription'];
}


