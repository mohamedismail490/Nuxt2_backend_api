<?php
namespace App\Repositories;

use App\Models\Topic;
use Exception;
use Illuminate\Support\Facades\DB;

class TopicRepository{

    public function getTopics($filter = []){
        if($filter){
            $filter = (object) $filter;
        }
        $topics = Topic::query()->with('user')->latest('created_at');
        $params = [];
        if(isset($filter->search_txt) && !empty($filter->search_txt)){
            $topics = $topics->where('title','like', '%'.$filter->search_txt.'%');
            $params['search_txt'] = $filter->search_txt;
        }
        if(isset($filter->paginate)){
            $topics = $topics->paginate(intval($filter->paginate));
            $params['paginate'] = intval($filter->paginate);
            if(!empty($params)){
                $topics = $topics->appends($params);
            }
            return $topics;
        }
        return $topics->get();
    }

    public function getTopicById($id, $filter = []){
        if($filter){
            $filter = (object) $filter;
        }
        $topic = Topic::where('id', $id);
        if(isset($filter->user_id) && !empty($filter->user_id) && (intval($filter->user_id) > 0)){
            $topic = $topic->where('user_id', intval($filter->user_id));
        }

        return $topic->firstOrFail();
    }

    public function createTopic($request): object
    {
        try{
            DB::beginTransaction();
            $topic = Topic::create([
                'user_id' => $request -> user() -> id,
                'title'   => $request -> title
            ]);
            if ($topic){
                $topic -> posts() -> create([
                    'user_id' => $request -> user() -> id,
                    'body'    => $request -> body
                ]);
            }

            DB::commit();
            return (object)[
                'status'  => true,
                'message' => 'Topic has been Created Successfully'
            ];
        }catch (Exception $e){
            DB::rollback();
            return (object)[
                'status'  => false,
                'message' => 'Something wrong happened! Please, try again.'
            ];
        }
    }

    public function updateTopic($id, $request, $userId): object
    {
        $topic = $this->getTopicById($id, [
            'user_id' => $userId
        ]);
        try{
            DB::beginTransaction();
            $topic->update([
                'title' => $request->title
            ]);

            DB::commit();
            return (object)[
                'status'  => true,
                'message' => 'Topic has been Updated Successfully'
            ];
        }catch (Exception $e){
            DB::rollback();
            return (object)[
                'status'  => false,
                'message' => 'Something wrong happened! Please, try again.'
            ];
        }
    }

    public function destroyTopic($id, $userId): object
    {
        $topic = $this->getTopicById($id, [
            'user_id' => $userId
        ]);
        try {
            DB::beginTransaction();
            $topic->delete();
            DB::commit();
            return (object)[
                'status'  => true,
                'message' => 'Topic has been Deleted Successfully'
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
