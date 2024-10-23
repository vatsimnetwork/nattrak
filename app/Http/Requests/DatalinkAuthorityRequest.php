<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatalinkAuthorityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'prefix' => ['required'],
            'auto_acknowledge_participant' => ['boolean'],
            'valid_rcl_target' => ['boolean'],
            'system' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
