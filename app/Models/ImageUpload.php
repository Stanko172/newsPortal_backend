<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'article_id',
        'is_title_image'
    ];
}
