<?php

namespace App\Policies;

use App\Models\Bulletin;
use App\Models\VatsimAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class BulletinPolicy
{
    use HandlesAuthorization;

    public function viewAny(?VatsimAccount $user): bool
    {
        return true;
    }

    public function view(?VatsimAccount $user): bool
    {
        return true;
    }

    public function create(VatsimAccount $user): bool
    {
        return $user->can('administrate');
    }

    public function update(VatsimAccount $user, Bulletin $bulletin): bool
    {
        return $user->can('administrate');
    }

    public function delete(VatsimAccount $user, Bulletin $bulletin): bool
    {
        return $user->can('administrate');
    }

    public function restore(VatsimAccount $user, Bulletin $bulletin): bool
    {
        return $user->can('administrate');
    }

    public function forceDelete(VatsimAccount $user, Bulletin $bulletin): bool
    {
        return $user->can('administrate');
    }
}
