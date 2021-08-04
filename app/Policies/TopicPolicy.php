<?php

namespace App\Policies;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function update(User $user, Topic $topic): bool
    {
        return $user -> ownsTopic($topic);
    }

    public function destroy(User $user, Topic $topic): bool
    {
        return $user -> ownsTopic($topic);
    }
}
