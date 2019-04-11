<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];
    
    public function medias() {
        return $this->belongsToMany(Media::class, 'media_tag');
    }
}
