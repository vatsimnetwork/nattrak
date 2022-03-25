<?php

namespace App\Services;

use App\Models\VatsimAccount;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class VatsimDataService
{
    const NETWORK_DATA_URL = "https://data.vatsim.net/v3/vatsim-data.json";

    private function getNetworkData()
    {
        $networkResponse = null;
        try
        {
            $networkResponse = Http::timeout(10)->get(self::NETWORK_DATA_URL);
        }
        catch (\Exception $ex)
        {
            Log::warning('Failed to download network data, exception was ' . $exception->getMessage());
            return null;
        }

        if (!$networkResponse->successful()) {
            Log::warning('Failed to download network data, response was ' . $networkResponse->status());
            return null;
        }

        return json_decode($networkResponse);
    }

    public function isActivePilot(VatsimAccount $vatsimAccount): bool
    {
        $networkData = $this->getNetworkData();
        return (in_array($vatsimAccount->id, array_column($networkData->pilots, 'cid')));
    }

    public function isActiveController()
    {

    }

    public function getActivePilotData(VatsimAccount $vatsimAccount)
    {
        $networkData = $this->getNetworkData();
        if (in_array($vatsimAccount->id, array_column($networkData->pilots, 'cid'))) {
            $key = (array_search($vatsimAccount->id, array_column($networkData->pilots, 'cid')));
            $data = $networkData->pilots[$key];
            return $data;
        } else {
            return null;
        }
    }
}
