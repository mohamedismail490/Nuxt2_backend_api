<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Http\Requests\TopicUpdateRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Repositories\TopicRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TopicController extends Controller
{
    public $topicRepo;
    public function __construct(TopicRepository $topicRepo){
        $this->topicRepo = $topicRepo;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $topics = $this->topicRepo->getTopics([
            'search_txt' => $request -> input('search_txt'),
            'paginate'   => !empty($request -> input('paginate')) ? $request -> input('paginate') : 5,
        ]);
        return TopicResource::collection($topics);
    }

    public function store(TopicRequest $request): JsonResponse
    {
        $create = $this->topicRepo->createTopic($request);
        return response() -> json($create);
    }

    public function show(Topic $topic): TopicResource
    {
        $topic = $this->topicRepo->getTopicById($topic->id);
        return new TopicResource($topic);
    }

    public function edit(Topic $topic): TopicResource
    {
        $topic = $this->topicRepo->getTopicById($topic->id, [
            'user_id' => auth() -> user() -> id
        ]);
        return new TopicResource($topic);
    }

    public function update(TopicUpdateRequest $request, Topic $topic): JsonResponse
    {
        $this -> authorize('update', $topic);
        $update = $this->topicRepo->updateTopic($topic->id, $request, auth() -> user() -> id);
        return response() -> json($update);
    }

    public function destroy(Topic $topic): JsonResponse
    {
        $this -> authorize('destroy', $topic);
        $destroy = $this->topicRepo->destroyTopic($topic->id, auth() -> user() -> id);
        return response() -> json($destroy);
    }

}
