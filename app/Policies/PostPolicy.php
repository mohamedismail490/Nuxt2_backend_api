<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function update(User $user, Post $post): bool
    {
        return $user -> ownsPost($post);
    }

    public function destroy(User $user, Post $post): bool
    {
        return $user -> ownsPost($post);
    }

    public function like(User $user, Post $post): bool
    {
        return !$user -> ownsPost($post);
    }
}
