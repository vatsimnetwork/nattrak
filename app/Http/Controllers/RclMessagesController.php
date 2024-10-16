<?php

namespace App\Http\Controllers;

use App\Enums\DatalinkAuthorities;
use App\Enums\RclResponsesEnum;
use App\Http\Requests\RclMessageRequest;
use App\Models\RclMessage;
use App\Models\Track;
use App\Services\CpdlcService;
use App\Services\VatsimDataService;
use Illuminate\Support\Facades\Auth;

class RclMessagesController extends Controller
{
    private $dataService;
    public CpdlcService $cpdlcService;

    public function __construct(VatsimDataService $vatsimDataService, CpdlcService $cpdlcService)
    {
        $this->dataService = $vatsimDataService;
        $this->cpdlcService = $cpdlcService;
    }

    public function index()
    {
        return view('pilots.rcl.index', [
            'pendingRclExists' => RclMessage::whereVatsimAccountId(Auth::id())->whereClxMessageId(null)->exists()
        ]);
    }

    public function create()
    {
        $data = $this->dataService->getActivePilotData(Auth::user());
        $isConcorde = $data?->flight_plan?->aircraft_short == 'CONC';

        return view('pilots.rcl.create', [
            'callsign' => $data?->callsign ?? null,
            'flight_level' => substr($data?->flight_plan?->altitude, 0, 3) ?? null,
            'arrival_icao' => $data?->flight_plan?->arrival ?? null,
            'tracks' => Track::whereActive(true)->when($isConcorde, fn ($query) => $query->orWhere('concorde', true))->get(),
            'isConcorde' => $isConcorde,
            '_pageTitle' => 'Request Oceanic Clearance',
        ]);
    }

    public function store(RclMessageRequest $request)
    {
        $rclMessage = new RclMessage($request->all());
        $rclMessage->vatsim_account_id = $request->user()->id;
        $rclMessage->request_time = now();
        $rclMessage->atc_rejected = false;
        if ($previousRcl = RclMessage::whereVatsimAccountId($request->user()->id)->whereClxMessageId(null)->first()) {
            if ($previousRcl->exists()) {
                $rclMessage->re_request = true;
                $previousRcl->delete();
            }
        }
        $rclMessage->save();

        // If RCL auto acknowledgement enabled, send CPDLC acknowledgement
        if (config('app.rcl_auto_acknowledgement_enabled')) {
            $rclMessage->acknowledged_at = now();
            $rclMessage->is_acknowledged = true;
            $rclMessage->save();
            $this->cpdlcService->sendMessage(
                author: DatalinkAuthorities::SYS,
                recipient: $rclMessage->callsign,
                recipientAccount: $rclMessage->vatsimAccount,
                message: sprintf(RclResponsesEnum::Acknowledge->value, strtoupper(DatalinkAuthorities::SYS->description())),
                caption: RclResponsesEnum::Acknowledge->text()
            );
        }

        return redirect()->route('pilots.message-history');
    }
}
