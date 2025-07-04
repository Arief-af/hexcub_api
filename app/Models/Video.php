<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'duration',
        'materi' ,
        'file',
    ];
    public function videoDetails()
    {
        return $this->hasMany(VideoDetail::class);
    }
}
