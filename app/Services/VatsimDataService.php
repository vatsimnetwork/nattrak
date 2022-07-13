<?php

namespace App\Services;

use App\Enums\AccessLevelEnum;
use App\Enums\DatalinkAuthorities;
use App\Models\VatsimAccount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VatsimDataService
{
    public const NETWORK_DATA_URL = "https://data.vatsim.net/v3/vatsim-data.json";
    public const TRACK_API_ENDPOINT = "https://tracksv2.ganderoceanic.ca/current/tmi";

    private function getNetworkData()
    {
        return Cache::remember('vatsim-data', 30, function () {
            $request = Http::timeout(10)->get(self::NETWORK_DATA_URL);
            if (! $request->successful()) {
                Log::warning('Failed to download network data, response was ' . $request->status());
                return null;
            }
            return json_decode($request);
        });
    }

    public function getTmi(): ?string
    {
        if (config('services.tracks.override_tmi')) {
            return (string)config('services.tracks.override_tmi');
        }

        return Cache::remember('tmi', now()->addHours(1), function () {
            $tmiData = Http::acceptJson()->get(self::TRACK_API_ENDPOINT);
            if (! $tmiData) {
                return null;
            }
            return str_replace('"', '', $tmiData->body());
        });
    }

    public function isActivePilot(VatsimAccount $vatsimAccount): bool
    {
        $networkData = $this->getNetworkData();
        if (! $networkData) {
            return false;
        }
        return (in_array($vatsimAccount->id, array_column($networkData->pilots, 'cid')));
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

    public function isActiveOceanicController(VatsimAccount $vatsimAccount): bool
    {
        $networkData = $this->getNetworkData();
        if (! $networkData) {
            return false;
        }
        $online = in_array($vatsimAccount->id, array_column($networkData->controllers, 'cid'));

        if ($online) {
            $authorities = [];
            foreach (DatalinkAuthorities::cases() as $authority) {
                $authorities[] = $authority->value;
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

    public function getActiveControllerAuthority(VatsimAccount $vatsimAccount): ?DatalinkAuthorities
    {
        if (! $this->isActiveOceanicController($vatsimAccount)) {
            return null;
        }

        $callsignPrefix = strtok($this->getActiveControllerData($vatsimAccount)->callsign, '_');

        foreach (DatalinkAuthorities::cases() as $authority) {
            if ($callsignPrefix == $authority->value) {
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
            return $networkData->pilots[$key];
        } else {
            return null;
        }
    }
}
