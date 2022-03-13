<?php

namespace App\Policies;

use App\Models\RclMessage;
use App\Models\User;
use App\Models\VatsimAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class RclMessagePolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function viewAny(VatsimAccount $user): bool
    {
        return $user->can('administrate');
    }

    public function view(VatsimAccount $user, RclMessage $rclMessage): bool
    {
        return $rclMessage->vatsim_account_id == $user->id || $user->can('administrate');
    }

    public function create(VatsimAccount $user): bool
    {
        return $user->can('activePilot');
    }

    public function update(VatsimAccount $user, RclMessage $rclMessage): bool
    {
        $user->can('administrate');
    }

    public function delete(VatsimAccount $user, RclMessage $rclMessage): bool
    {
        $user->can('administrate');
    }

    public function restore(VatsimAccount $user, RclMessage $rclMessage): bool
    {
        $user->can('administrate');

    }

    public function forceDelete(VatsimAccount $user, RclMessage $rclMessage): bool
    {
        $user->can('administrate');
    }
}
