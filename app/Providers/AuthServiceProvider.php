<?php

namespace App\Providers;

use App\Enums\AccessLevelEnum;
use App\Models\VatsimAccount;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('administrate', function (VatsimAccount $account) {
            return $account->access_level == AccessLevelEnum::Administrator || AccessLevelEnum::Root;
        });

        Gate::define('activePilot', function (VatsimAccount $account) {
            if ($account->access_level == AccessLevelEnum::Administrator || AccessLevelEnum::Root) return true;
            return false;
        });

        Gate::define('activeController', function (VatsimAccount $account) {
            if ($account->access_level == AccessLevelEnum::Administrator || AccessLevelEnum::Root) return true;
            return false;
        });
    }
}
