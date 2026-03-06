<?php

namespace App\Http\Requests;

use App\Enums\DatalinkAuthorities;
use App\Enums\RclResponsesEnum;
use App\Models\DatalinkAuthority;
use App\Models\Track;
use App\Services\CpdlcService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RclMessageRequest extends FormRequest
{
    public CpdlcService $cpdlcService;

    public function __construct(CpdlcService $cpdlcService)
    {
        $this->cpdlcService = $cpdlcService;
    }

    public function rules(): array
    {
        return [
            'callsign' => 'required|string|max:7|alpha_num', Rule::unique('rcl_messages', 'callsign')->withoutTrashed(),
            'destination' => 'required|string|min:4|max:4|alpha',
            'flight_level' => 'required|numeric|digits:3|min:055',
            'max_flight_level' => 'required_if:is_concorde,false|numeric|digits:3|min:055|max:450',
            'upper_flight_level' => 'required_if:is_concorde,true|numeric|digits:3|min:055',
            'mach' => 'required|numeric|digits:3',
            'entry_fix' => 'required|max:5',
            'entry_time' => 'required|numeric|digits:4',
            'tmi' => 'required|numeric|min:001|max:366',
            'random_routeing' => 'nullable|regex:/^[A-Z\/0-9 _]*[A-Z\/0-9][A-Z\/0-9 _]*$/',
            'is_concorde' => 'nullable',
            'target_datalink_authority_id' => 'required',
        ];
    }

    protected $messages = [
        'mach.regex' => 'Mach must be in format 0xx (e.g. .74 = 074)',
        'flight_level.max' => 'You must file a valid flight level.',
        'max_flight_level.max' => 'You must file a valid maximum flight level.',
        'callsign.alpha_num' => 'Your callsign must be valid with no spaces as you would enter it into your pilot client. E.g. BAW14LA, AAL134',
        'target_datalink_authority_id.required' => 'You must select the first oceanic sector you will be flying through.'
    ];

    public function prepareForValidation()
    {
        if ($this->filled('random_routeing')) {
            $this->merge([
                'random_routeing' => $this->normaliseRouteing($this->get('random_routeing')),
            ]);
        }

        if ($this->shouldConvertMatchingRandomRouteingToTrack() && ($matchingTrack = $this->matchingTrackForRandomRouteing())) {
            $this->merge([
                'track_id' => $matchingTrack->id,
                'random_routeing' => null,
                'entry_fix' => strtok($matchingTrack->last_routeing, ' '),
            ]);
        }

        if (! $this->has('entry_fix') && $this->has('track_id')) {
            $this->merge([
                'entry_fix' => strtok(Track::whereId($this->get('track_id'))->firstOrFail()->last_routeing, ' '),
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            /**
             * Track/RR check
             */
            if ($this->track_id != null && $this->random_routeing != null) {
                $validator->errors()->add('select_one_routeing', 'You can only request either a NAT track or a random routeing. Check which one you are allocated in your CTP booking. (NAT Tracks are identified by a letter.)');
            } elseif ($this->track_id == null && $this->random_routeing == null) {
                $validator->errors()->add('select_one_routeing', 'You need to request either a NAT track or a random routeing. Check which one you are allocated in your CTP booking. (NAT Tracks are identified by a letter.)');
            } elseif ($this->shouldRejectMatchingRandomRouteing() && ($matchingTrack = $this->matchingTrackForRandomRouteing())) {
                $validator->errors()->add(
                    'random_routeing',
                    "Your requested random routeing exactly matches NAT Track {$matchingTrack->identifier}. Please re-submit your request and select Track {$matchingTrack->identifier} instead of RR."
                );
            }
            if (! $this->is_concorde) {
                /**
                 * Max FL > FL check
                 */
                if ($this->flight_level > $this->max_flight_level) {
                    $validator->errors()->add('max_fl', 'Your maximum flight level must be equal to or higher than your requested flight level.');
                }
                /**
                 * RVSM check
                 */
                if (in_array($this->flight_level, ['420', '440']) || in_array($this->max_flight_level, ['420', '440'])) {
                    $validator->errors()->add('rvsm', 'Your flight levels must be valid (420 and 440 are not valid).');
                }
                /** Max FL check */
                if ($this->flight_level > 450) {
                    $validator->errors()->add('flight_level.max', 'You must file a valid flight level.');
                }
                /** Mach regex */
                if (preg_match("/\b[0][1-9][0-9]\b/", $this->mach) == 0) {
                    $validator->errors()->add('mach.regex', 'Mach must be in format 0xx (e.g. .74 = 074)');
                }
                /** Entry fix time requirement */
                if (config('app.rcl_time_constraints_enabled') && strlen($this->entry_time) == 4) {
                    if (!$this->entryTimeWithinRange($this->entry_time)) {
                        if (config('app.rcl_auto_acknowledgement_enabled') && DatalinkAuthority::find($this->target_datalink_authority_id)?->auto_acknowledge_participant) {
                            $this->cpdlcService->sendMessage(author: DatalinkAuthority::find('SYST'), recipient: $this->callsign, recipientAccount: Auth::user(), message: sprintf(RclResponsesEnum::Contact->value, strtoupper(DatalinkAuthorities::OCEN->description())), caption: RclResponsesEnum::Contact->text());
                        }
                        $lower = config('app.rcl_lower_limit') + 1;
                        $upper = config('app.rcl_upper_limit') - 1;
                        $validator->errors()->add('entry_time.range', 'You are either too early or too late to submit oceanic clearance. If you are entering the oceanic more than ' . $upper . ' minutes from now, come back when within ' . $upper . ' minutes. If your entry is within ' . $lower . ' minutes, or you have already entered, request clearance via voice.');
                    }
                }
            }
        });
    }

    public function authorize(): bool
    {
        return Auth::user()->can('activePilot');
    }

    private function entryTimeWithinRange($input): bool
    {
        $currentDateTime = now();
        $entryTime = Carbon::createFromFormat('Hi', $input);

        // If the entry time is earlier than the current time, it must be on the next day
        if ($entryTime < $currentDateTime) {
            $entryTime->addDay();
        }

        // Calculate the difference in minutes between the current time and entry time
        $minutesDifference = $currentDateTime->diffInMinutes($entryTime);

        // Check if the difference is within the range [15, 90] minutes and not negative (entry time is in the future)
        if ($minutesDifference >= config('app.rcl_lower_limit') && $minutesDifference <= config('app.rcl_upper_limit')) {
            return true;
        }

        // Check if the estimated entry time is after midnight and the difference is within the range [15, 45] minutes
        $midnight = Carbon::today()->addDay(); // Get the midnight time for the next day
        $minutesToMidnight = $currentDateTime->diffInMinutes($midnight);
        $minutesFromMidnight = $entryTime->diffInMinutes($midnight);

        if ($minutesToMidnight >= config('app.rcl_lower_limit') && $minutesFromMidnight >= 0 && $minutesFromMidnight <= config('app.rcl_upper_limit')) {
            return true;
        }

        return false;
    }

    private function matchingTrackForRandomRouteing(): ?Track
    {
        if ($this->track_id != null || ! $this->filled('random_routeing')) {
            return null;
        }

        $requestedRouteing = $this->normaliseRouteing($this->random_routeing);
        if ($requestedRouteing === null) {
            return null;
        }

        $tracks = Track::query()
            ->when(
                $this->boolean('is_concorde'),
                fn ($query) => $query->where(function ($query) {
                    $query->where('active', true)->orWhere('concorde', true);
                }),
                fn ($query) => $query->where('active', true),
            )
            ->get();

        foreach ($tracks as $track) {
            if ($this->normaliseRouteing($track->last_routeing) === $requestedRouteing) {
                return $track;
            }
        }

        return null;
    }

    private function normaliseRouteing(?string $routeing): ?string
    {
        if ($routeing === null) {
            return null;
        }

        return preg_replace('/\s+/', ' ', strtoupper(trim($routeing)));
    }

    private function shouldRejectMatchingRandomRouteing(): bool
    {
        return $this->trackMatchingMode() === 'reject';
    }

    private function shouldConvertMatchingRandomRouteingToTrack(): bool
    {
        return $this->trackMatchingMode() === 'convert';
    }

    private function trackMatchingMode(): string
    {
        return strtolower((string) config('app.rcl_rr_matching_track_action', 'reject'));
    }
}
