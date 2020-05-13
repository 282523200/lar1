<?php

namespace App\Policies;
use App\Status;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
//自动注册 直接用  @can('destroy', $status) 或是 $this->authorize('destroy', $user);
    public function destroy(User $user, Status $status)
    {
        return $user->id === $status->user_id;
    }
}
