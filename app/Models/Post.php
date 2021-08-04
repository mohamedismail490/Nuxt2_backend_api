<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['body','user_id'];
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

    public function user() {
        return $this -> belongsTo(User::class)->withDefault();
    }

    public function topic() {
        return $this -> belongsTo(Topic::class)->withDefault();
    }
}
