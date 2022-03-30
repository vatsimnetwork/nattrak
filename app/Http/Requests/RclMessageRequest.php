<?php

namespace App\Http\Requests;

use App\Models\VatsimAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RclMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'callsign' => 'required',
            'destination' => 'required|string|max:4',
            'flight_level' => 'required|numeric|digits:3|max:410',
            'max_flight_level' => 'nullable|numeric|digits:3|max:410',
            'mach' => 'required|numeric|digits:3|regex:/\b[0][1-9]{2}\b/',
            'entry_fix' => 'required',
            'entry_time' => 'required|digits:4',
            'tmi' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'mach.regex' => 'Mach must be in format 0xx (e.g. .74 = 074)',
            'flight_level.max' => 'You must file an RVSM flight level.',
            'max_flight_level.max' => 'You must file an RVSM flight level for your maximum flight level.'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->track_id != null && $this->random_routeing != null) {
                $validator->errors()->add('select_one_routeing', 'You can only request either a NAT track or a random routeing. Check which one you are allocated in your CTP booking. (NAT Tracks are identified by a letter.)');
            } elseif ($this->track_id == null && $this->random_routeing == null) {
                $validator->errors()->add('select_one_routeing', 'You need to request either a NAT track or a random routeing. Check which one you are allocated in your CTP booking. (NAT Tracks are identified by a letter.)');
            }
        });
    }

    public function authorize(): bool
    {
        return Auth::user()->can('activePilot');
    }
}
