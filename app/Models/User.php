<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'phone', 'password','api_token', 'surname',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    public function photos()
    {
        return $this->hasMany(Photo::class, 'owner_id');
    }

    public function sharedPhoto()
    {
        return $this->belongsToMany(Photo::class, 'user_photo');
    }

    public function setApiToken()
    {
        $this->api_token = Str::random(60);
        $this->save();
        return $this->api_token;
    }
}
