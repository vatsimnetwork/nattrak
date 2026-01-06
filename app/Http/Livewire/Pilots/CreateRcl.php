<?php

namespace App\Http\Livewire\Pilots;

use App\Enums\DatalinkAuthorities;
use App\Models\DatalinkAuthority;
use App\Models\Track;
use App\Rules\FlightLevelRule;
use App\Services\VatsimDataService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateRcl extends Component
{
    public ?string $callsign;
    public $callsignPrefilled = false;
    public bool $isConcorde = false;
    public ?string $arrivalIcao;
    public bool $arrivalPrefilled = false;

    // Levels and speed
    #[Validate]
    public ?string $flightLevel;
    public ?string $maximumFlightLevel;
    public ?string $lowerBlockLevel;
    public ?string $upperBlockLevel;
    public ?string $machNumber;

    // Routing
    public ?string $datalinkAuthorityId;
    public string $routingMode = "";
    public ?string $trackId;
    public ?string $randomRouting;

    // Oceanic entry
    public ?string $oceanicEntryFix;
    public ?string $oceanicEta;

    // Misc
    public ?string $tmi;
    public ?string $freeText;


    public function mount()
    {
        $dataService = new VatsimDataService();
        $pilotData = $dataService->getActivePilotData(auth()->user());
        if ($pilotData?->callsign) $this->callsign = $pilotData->callsign && $this->callsignPrefilled = true;
        $this->isConcorde = $pilotData?->flight_plan?->aircraft_short == 'CONC';
        if ($pilotData?->arrival) $this->arrivalIcao = $pilotData->arrival && $this->arrivalPrefilled = true;
        $this->flightLevel = substr($pilotData?->flight_plan?->altitude, 0, 3) ?? null;
    }

    protected function rules(): array
    {
        return [
            'callsign' => 'required|string|max:7|alpha_num', Rule::unique('rcl_messages', 'callsign')->withoutTrashed(),
            'destination' => 'required|string|min:4|max:4|alpha',
            'flightLevel' => ['required_if:isConcorde,false|digits:3|numeric', new FlightLevelRule()],
            'maximumFlightLevel' => ['required_if:isConcorde,false|digits:3|numeric|greater_than_or_equal:flightLevel', new FlightLevelRule()],
            'lowerBlockLevel' => 'required_if:isConcorde,true|digits:3|numeric',
            'upperBlockLevel' => 'required_if:isConcorde,true|digits:3|numeric',
            'machNumber' => 'required|digits:3|numeric',
            'trackId' => 'prohibits:randomRouting',
            'randomRouting' => 'prohibits:trackId|regex:/^[A-Z\/0-9 _]*[A-Z\/0-9][A-Z\/0-9 _]*$/',
            'oceanicEntryFix' => 'required|max:5',
            'oceanicEta' => 'required|numeric|digits:4',
            'tmi' => 'required|numeric|min:001|max:366',
            'datalinkAuthorityId' => 'required',
        ];
    }

    protected $messages = [
        'mach.numeric' => 'Mach must be in format 0xx (e.g. .74 = 074)',
        'flight_level.digits' => 'You must file a valid flight level.',
        'max_flight_level.digits' => 'You must file a valid maximum flight level.',
        'callsign.alpha_num' => 'Your callsign must be valid with no spaces as you would enter it into your pilot client. E.g. BAW14LA, AAL134',
        'datalinkAuthorityId.required' => 'You must select the first oceanic sector you will be flying through.'
    ];

    public function render()
    {
        return view('livewire.pilots.create-rcl', [
            'tracks' => Track::whereActive(true)->when($this->isConcorde, fn ($query) => $query->orWhere('concorde', true))->get(),
            'datalinkAuthorities' => DatalinkAuthority::whereValidRclTarget(true)->pluck('name', 'id'),
        ]);
    }
}
