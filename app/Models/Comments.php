<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $table = 'comments';
    protected $fillable = ['user_id','post_id','comment','created_at','updated_at'];


    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}

}
