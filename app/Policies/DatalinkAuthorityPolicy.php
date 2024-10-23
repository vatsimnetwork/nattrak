<?php

namespace App\Policies;

use App\Models\DatalinkAuthority;
use App\Models\VatsimAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatalinkAuthorityPolicy
{
    use HandlesAuthorization;

    public function viewAny(VatsimAccount $user): bool
    {
        return $user->can('administrate');
    }

    public function view(VatsimAccount $user, DatalinkAuthority $datalinkAuthority): bool
    {
        return $user->can('administrate');

    }

    public function create(VatsimAccount $user): bool
    {
        return $user->can('administrate');

    }

    public function update(VatsimAccount $user, DatalinkAuthority $datalinkAuthority): bool
    {
        return $user->can('administrate');

    }

    public function delete(VatsimAccount $user, DatalinkAuthority $datalinkAuthority): bool
    {
        return $user->can('administrate');

    }

    public function restore(VatsimAccount $user, DatalinkAuthority $datalinkAuthority): bool
    {
        return $user->can('administrate');
    }

    public function forceDelete(VatsimAccount $user, DatalinkAuthority $datalinkAuthority): bool
    {
        return $user->can('administrate');
    }
}
