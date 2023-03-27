<?php

namespace App\Enums;

enum ConflictLevelEnum: int
{
    case None = 0;
    case Potential = 1;
    case Warning = 2;

    public function colour(): string
    {
        return match ($this) {
            ConflictLevelEnum::None => 'conflict-green',
            ConflictLevelEnum::Potential => 'conflict-potential',
            ConflictLevelEnum::Warning => 'conflict-warning'
        };
    }
}
