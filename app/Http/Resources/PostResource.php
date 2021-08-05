<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this -> id,
            'topic_id'      => $this -> topic_id,
            'body'          => $this -> body,
            'created_since' => $this -> created_since,
            'user'          => new UserResource($this -> user),
            'likes_count'   => $this -> likes -> count(),
//            'likes_users'   => UserResource::collection($this -> likes -> pluck('user')),
            'liked_by_user' => auth() -> check() ? auth() -> user() -> hasLikedPost($this->resource) : false
        ];
    }
}
