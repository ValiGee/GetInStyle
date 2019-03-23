<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    public $fillable = ['name', 'model_path', 'image_path'];
    
    public function media() {
        return $this->hasMany(Media::class);
    }

    public function usingUsers() {
        return $this->belongsToMany(User::class, 'media');
    }
}
