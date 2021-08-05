<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Topic;
use App\Repositories\PostRepository;
use App\Repositories\TopicRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    public $topicRepo, $postRepo;
    public function __construct(TopicRepository $topicRepo, PostRepository $postRepo){
        $this->topicRepo = $topicRepo;
        $this->postRepo  = $postRepo;
    }

    public function index(Topic $topic, Request $request): AnonymousResourceCollection
    {
        $topic  = $this->topicRepo->getTopicById($topic->id);
        $posts = $this->postRepo->getPosts([
            'topic_id'   => $topic -> id,
            'search_txt' => $request -> input('search_txt'),
            'paginate'   => !empty($request -> input('paginate')) ? $request -> input('paginate') : 5,
        ]);
        return PostResource::collection($posts);
    }

    public function store(Topic $topic, PostRequest $request): JsonResponse
    {
        $topic  = $this->topicRepo->getTopicById($topic->id);
        $create = $this->postRepo->createPost($request, $topic);
        return response() -> json($create);
    }

    public function show(Topic $topic, Post $post): PostResource
    {
        $post = $this->postRepo->getPostById($post -> id, [
            'topic_id' => $topic -> id
        ]);
        return new PostResource($post);
    }

    public function edit(Topic $topic, Post $post): PostResource
    {
        $post = $this->postRepo->getPostById($post -> id, [
            'topic_id' => $topic -> id,
            'user_id'  => auth() -> user() -> id
        ]);
        return new PostResource($post);
    }

    public function update(PostRequest $request, Topic $topic, Post $post): JsonResponse
    {
        $this -> authorize('update', $post);
        $update = $this->postRepo->updatePost($post -> id, $request, $topic -> id);
        return response() -> json($update);
    }

    public function destroy(Topic $topic, Post $post): JsonResponse
    {
        $this -> authorize('destroy', $post);
        $destroy = $this->postRepo->destroyPost($post -> id, $topic -> id, auth() -> user() -> id);
        return response() -> json($destroy);
    }

    public function toggleLike(Topic $topic, Post $post): JsonResponse
    {
//        $this -> authorize('like', $post);
        $post = $this->postRepo->getPostById($post -> id, [
            'topic_id' => $topic -> id
        ]);
        $toggle = $this->postRepo->toggleLike($post, auth() -> user() -> id);
        return response() -> json($toggle);
    }

}
