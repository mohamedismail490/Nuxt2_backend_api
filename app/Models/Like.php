<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id'];
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


    public function likeable() {
        return $this->morphTo();
    }

    public function user() {
        return $this -> belongsTo(User::class)->withDefault();
    }
}
