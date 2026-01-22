<?php

namespace App\Policies;

use App\Models\TimeSession;
use App\Models\User;

class TimeSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TimeSession $timeSession): bool
    {
        return $user->id === $timeSession->task->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TimeSession $timeSession): bool
    {
        return $user->id === $timeSession->task->user_id;
    }

    public function delete(User $user, TimeSession $timeSession): bool
    {
        return $user->id === $timeSession->task->user_id;
    }
}
