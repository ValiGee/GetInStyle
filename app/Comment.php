<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $fillable = ['user_id', 'media_id', 'message', 'parent_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function media() {
        return $this->belongsTo(Media::class);
    }

    public function likes() {
        return $this->morphMany(Like::class, 'likable');
    }

    public function parent() {
        return $this->belongsTo(Comment::class);
    }

    public function replies() {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
