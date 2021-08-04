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
            'user'          => new UserResource($this -> user)
        ];
    }
}
