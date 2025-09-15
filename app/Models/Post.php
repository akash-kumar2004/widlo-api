<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
      protected $fillable = [
        'user_id',
        'post_details',
        'img',
        'lat',
        'lng',
        'title',
        'parent_post_id',
        'category_id'
    ];

    public function likes()
{
    return $this->hasMany(Comments::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function comments()
{
    return $this->hasMany(Comments::class);
}

public function category()
{
    return $this->belongsTo(Category::class);
}

}
