<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    public $fillable = ['user_id', 'path', 'style_id', 'stylized_path'];

    public function style() {
        return $this->belongsTo(Style::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function commentingUsers() {
        return $this->belongsToMany(User::class, 'comments');
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function likingUsers() {
        return $this->belongsToMany(User::class, 'likes');
    }
}
