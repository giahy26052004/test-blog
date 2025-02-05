<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'views'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($post) {
            foreach ($post->categories as $category) {
                $category->update(['views' => $category->posts()->sum('views')]);
            }
        });
    }
}
