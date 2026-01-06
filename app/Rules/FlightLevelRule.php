<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FlightLevelRule implements ValidationRule
{
    protected $data = [];


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // rvsm
        if (in_array($value, ['420', '440'])) {
            $fail('The :attribute fails RVSM requirements. (FL420 and FL440 are not valid levels.');
        }
        // above oceanic max
        elseif ($value > 450) {
            $fail('You must file a valid :attribute.');
        }
    }
}
