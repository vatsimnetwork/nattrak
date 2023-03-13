<?php

namespace App\Services;

use App\Enums\AccessLevelEnum;
use App\Enums\DatalinkAuthorities;
use App\Models\VatsimAccount;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VatsimDataService
{
    const NETWORK_DATA_URL = "https://data.vatsim.net/v3/vatsim-data.json";
    const TRACK_API_ENDPOINT = "https://tracks.ganderoceanic.ca/data";

    private function getNetworkData()
    {
        $networkResponse = Cache::remember('vatsim-data', 30, function () {
            $request = Http::timeout(10)->get(self::NETWORK_DATA_URL);
            if (! $request->successful()) {
                Log::warning('Failed to download network data, response was ' . $request->status());
                return null;
            }
            return json_decode($request);
        });

        return $networkResponse;
    }

    public function getTmi(): ?string
    {
        if (config('services.tracks.override_tmi')) {
            return (string)config('services.tracks.override_tmi');
        }

        return Cache::remember('tmi', now()->addHours(1), function () {
            $trackData = Http::get(self::TRACK_API_ENDPOINT, [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            if ($trackData) {
                $tracks = json_decode(($trackData));
            } else {
                return null;
            }

            if (! $tracks[0]) return null;

            return $tracks[0]->tmi;
        });
    }

    public function isActivePilot(VatsimAccount $vatsimAccount): bool
    {
        //TODO: REMOVE FOR DEV
        return true;
        $networkData = $this->getNetworkData();
        if (! $networkData) return false;
        return (in_array($vatsimAccount->id, array_column($networkData->pilots, 'cid')));
    }

    public function getActiveControllerData(VatsimAccount $vatsimAccount)
    {
        $networkData = $this->getNetworkData();
        if (! $networkData) return null;
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
        //TODO: REMOVE FOR DEV
        return true;
        $networkData = $this->getNetworkData();
        if (! $networkData) return false;
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

    public function getActiveControllerAuthority(VatsimAccount $vatsimAccount)
    {
        if (! $this->isActiveOceanicController($vatsimAccount) || $this->getActiveControllerData($vatsimAccount) == null) return null;

        $callsignPrefix = strtok($this->getActiveControllerData($vatsimAccount)->callsign, '_');

        foreach (DatalinkAuthorities::cases() as $authority) {
            if ($callsignPrefix == $authority->value) return $authority;
        }

        return null;
    }

    public function getActivePilotData(VatsimAccount $vatsimAccount)
    {
        $networkData = $this->getNetworkData();
        if (! $networkData) return null;
        $vatsimAccount->id = 899571;
        if (in_array($vatsimAccount->id, array_column($networkData->pilots, 'cid'))) {
            $key = (array_search($vatsimAccount->id, array_column($networkData->pilots, 'cid')));
            $data = $networkData->pilots[$key];
            return $data;
        } else {
            return null;
        }
    }
}
