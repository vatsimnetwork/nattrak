<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulletinRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
            'subtitle' => ['nullable'],
            'content' => ['required'],
            'action_url' => ['nullable', 'url'],
            'alert_controllers' => ['nullable'],
            'alert_pilots' => ['nullable'],
        ];
    }
}
