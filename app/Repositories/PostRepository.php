<?php
namespace App\Repositories;

use App\Http\Resources\PostResource;
use App\Models\Like;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;

class PostRepository{

    public function getPosts($filter = []){
        if($filter){
            $filter = (object) $filter;
        }
        $posts = Post::query()->with('user')->latest('created_at');
        $params = [];
        if(isset($filter -> topic_id) && !empty($filter -> topic_id) && (intval($filter -> topic_id) > 0)){
            $posts = $posts->where('topic_id',intval($filter -> topic_id));
        }
        if(isset($filter->search_txt) && !empty($filter->search_txt)){
            $posts = $posts->where('body','like', '%'.$filter->search_txt.'%');
            $params['search_txt'] = $filter->search_txt;
        }
        if(isset($filter->paginate)){
            $posts = $posts->paginate(intval($filter->paginate));
            $params['paginate'] = intval($filter->paginate);
            if(!empty($params)){
                $posts = $posts->appends($params);
            }
            return $posts;
        }
        return $posts->get();
    }

    public function getPostById($id, $filter = []){
        if($filter){
            $filter = (object) $filter;
        }
        $post = Post::where('id', $id);
        if(isset($filter->user_id) && !empty($filter->user_id) && (intval($filter->user_id) > 0)){
            $post = $post->where('user_id', intval($filter->user_id));
        }
        if(isset($filter->topic_id) && !empty($filter->topic_id) && (intval($filter->topic_id) > 0)){
            $post = $post->where('topic_id', intval($filter->topic_id));
        }

        return $post->firstOrFail();
    }

    public function createPost($request,$topic): object
    {
        try{
            DB::beginTransaction();
            $topic -> posts() -> create([
                'user_id' => $request -> user() -> id,
                'body'    => $request -> body,
            ]);
            DB::commit();
            return (object)[
                'status'  => true,
                'message' => 'Post has been Created Successfully'
            ];
        }catch (Exception $e){
            DB::rollback();
            return (object)[
                'status'  => false,
                'message' => 'Something wrong happened! Please, try again.'
            ];
        }
    }

    public function updatePost($id, $request, $topicId): object
    {
        $post = $this->getPostById($id, [
            'user_id'  => $request -> user() -> id,
            'topic_id' => $topicId
        ]);
        try{
            DB::beginTransaction();
            $post -> update([
                'body' => $request -> body
            ]);

            DB::commit();
            return (object)[
                'status'  => true,
                'message' => 'Post has been Updated Successfully'
            ];
        }catch (Exception $e){
            DB::rollback();
            return (object)[
                'status'  => false,
                'message' => 'Something wrong happened! Please, try again.'
            ];
        }
    }

    public function destroyPost($id, $topicId, $userId): object
    {
        $post = $this->getPostById($id, [
            'user_id'  => $userId,
            'topic_id' => $topicId
        ]);
        try {
            DB::beginTransaction();
            $post->delete();
            DB::commit();
            return (object)[
                'status'  => true,
                'message' => 'Post has been Deleted Successfully'
            ];
        }catch (Exception $e){
            DB::rollback();
            return (object)[
                'status'  => false,
                'message' => 'Something wrong happened! Please, try again.'
            ];
        }
    }

    public function toggleLike($post, $userId): object
    {
        if ($post -> user_id == $userId) {
            return (object)[
                'status'  => false,
                'message' => 'You Can\'t Like Your Own Post!'
            ];
        }
        $isAlreadyLiked = false;
        $existPostLike = Like::query()->where('likeable_id', $post -> id)
            ->where('user_id', $userId)
            ->where('likeable_type', 'App\Models\Post')
            ->first();
        if (!empty($existPostLike)){
            $isAlreadyLiked = true;
        }
        $postLikesCount = $post -> likes -> count();
        try{
            DB::beginTransaction();
            if ($isAlreadyLiked) {
                $existPostLike->delete();
            }else {
                $post -> likes() -> create([
                    'user_id' => $userId
                ]);
            }
            $isNowLiked = !$isAlreadyLiked;
            DB::commit();
            return (object)[
                'status'      => true,
                'message'     => $isNowLiked ? 'Post Liked' : 'Post Unliked',
                'is_liked'    => $isNowLiked,
                'likes_count' => $isNowLiked ? ($postLikesCount + 1) : (($postLikesCount > 0) ? ($postLikesCount - 1) : 0),
            ];
        }catch (Exception $e){
            DB::rollback();
            return (object)[
                'status'  => false,
                'message' => 'Something wrong happened! Please, try again.'
            ];
        }
    }
}
