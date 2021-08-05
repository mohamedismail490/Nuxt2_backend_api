<?php

namespace App\Models;

use Carbon\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends  = ['created_since'];

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d-m-Y g:i:s A');
    }
    public function getUpdatedAtAttribute($value){
        return Carbon::parse($value)->format('d-m-Y g:i:s A');
    }
    public function getCreatedSinceAttribute(){
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function ownsTopic(Topic $topic): bool
    {
        return $this -> id === $topic -> user_id;
    }

    public function ownsPost(Post $post): bool
    {
        return $this -> id === $post -> user_id;
    }

    public function hasLikedPost(Post $post) {
        return $post->likes->where('user_id', $this->id)->count() >= 1;
    }
}
