<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\ImageUpload;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'views',
        'recommended',
        'category_id',
        'user_id'
    ];

    public function author(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function image_uploads(){
        return $this->hasMany(ImageUpload::class);
    }
}
