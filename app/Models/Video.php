<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_link',
        'thumbnail',
        'file_size',
        'duration',
    
    ];


}