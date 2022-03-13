<?php

namespace App\Policies;

use App\Models\ClxMessage;
use App\Models\User;
use App\Models\VatsimAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClxMessagePolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function viewAny(VatsimAccount $user): bool
    {
        return $user->can('activeController');
    }

    public function view(VatsimAccount $user, ClxMessage $clxMessage): bool
    {
        return $user->can('activeController');
    }

    public function create(VatsimAccount $user): bool
    {
        return $user->can('activeController');
    }

    public function update(VatsimAccount $user, ClxMessage $clxMessage): bool
    {
        return $user->can('activeController');
    }

    public function delete(VatsimAccount $user, ClxMessage $clxMessage): bool
    {
        return $user->can('activeController');
    }

    public function restore(VatsimAccount $user, ClxMessage $clxMessage): bool
    {
        return $user->can('activeController');
    }

    public function forceDelete(VatsimAccount $user, ClxMessage $clxMessage): bool
    {
        return $user->can('activeController');
    }
}
