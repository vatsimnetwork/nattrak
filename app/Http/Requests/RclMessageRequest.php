<?php

namespace App\Http\Requests;

use App\Models\VatsimAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RclMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'callsign' => 'required|string|max:7|alpha_num|unique:rcl_messages,callsign',
            'destination' => 'required|string|min:4|max:4|alpha',
            'flight_level' => 'nullable|numeric|digits:3|min:055|max:450',
            'max_flight_level' => 'nullable|numeric|digits:3|min:055|max:450',
            'mach' => 'required|numeric|digits:3|regex:/\b[0][1-9][0-9]\b/',
            'entry_fix' => 'required',
            'entry_time' => 'required|digits:4',
            'tmi' => 'required|numeric|min:001|max:366',
        ];
    }

    public function messages()
    {
        return [
            'mach.regex' => 'Mach must be in format 0xx (e.g. .74 = 074)',
            'flight_level.max' => 'You must file a valid flight level.',
            'max_flight_level.max' => 'You must file a valid maximum flight level.',
            'callsign.alpha_num' => 'Your callsign must be valid with no spaces as you would enter it into your pilot client. E.g. BAW14LA, AAL134'
        ];
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
            }
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
                $validator->errors()->add('rvsm', 'Your flight levels must be valid (420 and 440 are not valid).s');
            }
        });
    }

    public function authorize(): bool
    {
        return Auth::user()->can('activePilot');
    }
}
