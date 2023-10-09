<?php

namespace App\Enums;

enum ClxCancellationReasons: string
{
    case Superseded = 'superseded';
    case NewEta = 'neweta';

    public function text(): string
    {
        return match ($this) {
            self::Superseded => 'Superseded by a new clearance.',
            self::NewEta => 'Cancelled due new ETA notification.'
        };
    }
}
