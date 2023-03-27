<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClxMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'atc_fl' => 'nullable|numeric|digits:3',
            'atc_mach' => 'nullable|numeric|digits:3',
            'free_text' => 'nullable',
            'entry_time_type' => 'nullable|required_with:entry_time_requirement', Rule::in(['<', '=', '>']),
            'entry_time_requirement' => 'nullable|required_with:entry_time_type|digits:4|numeric',
            'datalink_authority' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->new_track_id != null && $this->new_random_routeing != null) {
                $validator->errors()->add('select_one_routeing', 'ROUTE: Select only NAT or RR');
            }
        });
    }

    public function authorize(): bool
    {
        return Auth::user()->can('activeController');
    }
}
