<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ClxMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'atc_fl' => 'nullable|numeric|digits:3',
            'atc_mach' => 'nullable|numeric|digits:3',
            'free_text' => 'nullable',
            'entry_time_requirement' => 'nullable',
            'datalink_authority' => 'required'
        ];
    }

    public function authorize(): bool
    {
        return Auth::user()->can('activeController');
    }
}
