<?php

namespace App\Services;

use App\Enums\AccessLevelEnum;
use App\Enums\DatalinkAuthorities;
use App\Enums\DomesticAuthorities;
use App\Models\DatalinkAuthority;
use App\Models\VatsimAccount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VatsimDataService
{
    public const NETWORK_DATA_URL = 'https://data.vatsim.net/v3/vatsim-data.json';

    private ?object $networkData = null;

    private function getNetworkData()
    {
        if ($this->networkData) {
            return $this->networkData;
        }

        $this->networkData = Cache::remember('vatsim-data', 30, function () {
            $request = Http::timeout(10)->get(self::NETWORK_DATA_URL);
            if (! $request->successful()) {
                Log::warning('Failed to download network data, response was '.$request->status());

                return null;
            }

            return json_decode($request);
        });

        return $this->networkData;
    }

    public function isActivePilot(VatsimAccount $vatsimAccount): bool
    {
        if (! auth()->check()) {
            return false;
        }
        $networkData = $this->getNetworkData();
        if (! $networkData) {
            return false;
        }

        return in_array($vatsimAccount->id, array_column($networkData->pilots, 'cid'));
    }

    public function getActiveControllerData(VatsimAccount $vatsimAccount)
    {
        $networkData = $this->getNetworkData();
        if (! $networkData) {
            return null;
        }
        if (in_array($vatsimAccount->id, array_column($networkData->controllers, 'cid'))) {
            $key = (array_search($vatsimAccount->id, array_column($networkData->controllers, 'cid')));
            $data = $networkData->controllers[$key];

            return $data;
        } else {
            return null;
        }
    }

    public function isActiveOceanicController(VatsimAccount $vatsimAccount)
    {
        if (! auth()->check()) {
            return false;
        }
        $networkData = $this->getNetworkData();
        if (! $networkData) {
            return false;
        }
        $online = in_array($vatsimAccount->id, array_column($networkData->controllers, 'cid'));

        if ($online) {
            $authorities = [];
            foreach (DatalinkAuthority::all() as $authority) {
                $authorities[] = $authority->prefix;
            }

            $callsign = $this->getActiveControllerData($vatsimAccount)->callsign;
            if (in_array(strtok($callsign, '_'), $authorities)) {
                return true;
            } else {
                return $vatsimAccount->access_level == AccessLevelEnum::Controller;
            }
        }

        return false;
    }

    public function isActiveBoundaryDomesticController(VatsimAccount $vatsimAccount)
    {
        if (! auth()->check()) {
            return false;
        }
        $networkData = $this->getNetworkData();
        if (! $networkData) {
            return false;
        }
        $online = in_array($vatsimAccount->id, array_column($networkData->controllers, 'cid'));

        if ($online) {
            $authorities = [];
            foreach (DatalinkAuthority::all() as $authority) {
                $authorities[] = $authority->prefix;
            }}
            $callsign = $this->getActiveControllerData($vatsimAccount)->callsign;
            if ((in_array(strtok($callsign, '_'), $authorities)) || (in_array(strtok($callsign, '-'), $authorities))) {
                return true;
            } else {
                return $vatsimAccount->access_level == AccessLevelEnum::Controller;
            }
        }

        return false;
    }

    public function getActiveControllerAuthority(VatsimAccount $vatsimAccount)
    {
        if (! $this->isActiveOceanicController($vatsimAccount) || $this->getActiveControllerData($vatsimAccount) == null) {
            return null;
        }

        $callsignPrefix = strtok($this->getActiveControllerData($vatsimAccount)->callsign, '_');

        $authority = DatalinkAuthority::wherePrefix($callsignPrefix)->first();
        if ($authority) {
            return $authority;
        }
        return null;
    }

    public function getActiveDomesticControllerAuthority(VatsimAccount $vatsimAccount)
    {
        if (! $this->isActiveBoundaryDomesticController($vatsimAccount) || $this->getActiveControllerData($vatsimAccount) == null) {
            return null;
        }

        $callsignPrefixUnderscore = strtok($this->getActiveControllerData($vatsimAccount)->callsign, '_');
        $callsignPrefixDash = strtok($this->getActiveControllerData($vatsimAccount)->callsign, '-');

        foreach (DomesticAuthorities::cases() as $authority) {
            if ($callsignPrefixUnderscore == $authority->value || $callsignPrefixDash == $authority->value) {
                return $authority;
            }
        }

        return null;
    }

    public function getActivePilotData(VatsimAccount $vatsimAccount)
    {
        $networkData = $this->getNetworkData();
        if (! $networkData) {
            return null;
        }
        if (in_array($vatsimAccount->id, array_column($networkData->pilots, 'cid'))) {
            $key = (array_search($vatsimAccount->id, array_column($networkData->pilots, 'cid')));
            $data = $networkData->pilots[$key];

            return $data;
        } else {
            return null;
        }
    }

    public function getVatsimAccountByCallsign($callsign)
    {
        $networkData = $this->getNetworkData();
        if (! $networkData) {
            return null;
        }
        if (in_array(strtoupper($callsign), array_column($networkData->pilots, 'callsign'))) {
            $key = (array_search(strtoupper($callsign), array_column($networkData->pilots, 'callsign')));
            $data = $networkData->pilots[$key];

            return $data;
        } else {
            return null;
        }
    }
}
