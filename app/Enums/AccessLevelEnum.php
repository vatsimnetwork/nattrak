<?php

namespace App\Enums;

/**
 * Access level for VATSIM accounts on natTrak.
 */
enum AccessLevelEnum: int
{
    case Pilot = 0;
    case Controller = 1;
    case Administrator = 2;
    case Root = 3;

    public function labelPowergridFilter(): string
    {
        return $this->name;
    }
}
