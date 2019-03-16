<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function media() {
        return $this->hasMany(Media::class);
    }

    public function usedStyles() {
        return $this->belongsToMany(Style::class, 'media');
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function commentedMedia() {
        return $this->belongsToMany(Media::class, 'comments');
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function likedMedia() {
        return $this->belongsToMany(Media::class, 'likes');
    }
}
