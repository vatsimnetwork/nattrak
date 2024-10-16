<?php

namespace App\Providers;

use App\Enums\AccessLevelEnum;
use App\Models\ClxMessage;
use App\Models\RclMessage;
use App\Models\VatsimAccount;
use App\Policies\ClxMessagePolicy;
use App\Policies\RclMessagePolicy;
use App\Services\VatsimDataService;
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
        RclMessage::class => RclMessagePolicy::class,
        ClxMessage::class => ClxMessagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $dataService = new VatsimDataService();

        Gate::before(function (VatsimAccount $account) {
            return ($account->access_level == AccessLevelEnum::Administrator || $account->access_level == AccessLevelEnum::Root) ? true : null;
        });

        Gate::define('administrate', function (VatsimAccount $account) {
            return $account->settings()->get('user-mode') == AccessLevelEnum::Administrator;
        });

        Gate::define('activePilot', function (VatsimAccount $account) use ($dataService) {
            return $account->settings()->get('user-mode') == AccessLevelEnum::Pilot && $dataService->isActivePilot($account);
        });

        Gate::define('activeController', function (VatsimAccount $account) use ($dataService) {
            return $account->settings()->get('user-mode') == AccessLevelEnum::Controller && $dataService->isActiveOceanicController($account);
        });

        Gate::define('activeBoundaryController', function (VatsimAccount $account) use ($dataService) {
            return $account->settings()->get('user-mode') == AccessLevelEnum::Controller && $dataService->isActiveBoundaryDomesticController($account);
        });
    }
}
