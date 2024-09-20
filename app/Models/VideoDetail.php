<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'title',
        'time',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}