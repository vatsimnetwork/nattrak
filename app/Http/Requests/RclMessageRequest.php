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
            'flight_level' => 'required|numeric|digits:3',
            'max_flight_level' => 'nullable|numeric|digits:3',
            'mach' => 'required|numeric|digits:3',
            'entry_fix' => 'required',
            'entry_time' => 'required|digits:4',
            'tmi' => 'required|numeric',
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
