<?php

namespace App\Http\Controllers;

use App\Http\Requests\RclMessageRequest;
use App\Models\RclMessage;
use App\Models\Track;
use App\Services\VatsimDataService;
use Illuminate\Support\Facades\Auth;

class RclMessagesController extends Controller
{
    private $dataService;

    public function __construct(VatsimDataService $vatsimDataService)
    {
        $this->dataService = $vatsimDataService;
    }

    public function index()
    {
        return view('pilots.rcl.index');
    }

    public function create()
    {
        if (RclMessage::whereVatsimAccountId(Auth::id())->whereClxMessageId(null)->exists()) {
            toastr()->error('You already have a pending oceanic clearance request. If it has been waiting for more than 10 minutes, let the controller know.');
            return redirect()->route('pilots.rcl.index');
        }

        $data = $this->dataService->getActivePilotData(Auth::user());
        $isConcorde = $data?->flight_plan?->aircraft_short == 'CONC';
        return view('pilots.rcl.create', [
            'callsign' => $data?->callsign ?? null,
            'flight_level' => substr($data?->flight_plan?->altitude, 0, 3) ?? null,
            'arrival_icao' => $data?->flight_plan?->arrival ?? null,
            'tracks' => Track::whereActive(true)->when($isConcorde, fn ($query) => $query->orWhere('concorde', true)) ->get(),
            'isConcorde' => $isConcorde,
            '_pageTitle' => 'Request Oceanic Clearance'
        ]);
    }

    public function store(RclMessageRequest $request)
    {
        if (RclMessage::whereVatsimAccountId($request->user()->id)->whereClxMessageId(null)->exists()) {
            toastr()->error('You already have a pending oceanic clearance request. If it has been waiting for more than 10 minutes, let the controller know.');
            return redirect()->route('pilots.rcl.index');
        }

        $rclMessage = new RclMessage($request->all());
        $rclMessage->vatsim_account_id = $request->user()->id;
        $rclMessage->request_time = now();
        $rclMessage->atc_rejected = false;
        $rclMessage->save();

        toastr()->info('Your messaged has been received. Accept notifications from this site and we can let you know when it\'s been replied to!');
        return redirect()->route('pilots.message-history');
    }
}
