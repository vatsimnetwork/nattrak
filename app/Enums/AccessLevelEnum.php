<?php

namespace App\Enums;

enum AccessLevelEnum: int
{
    case Pilot = 0;
    case Controller = 1;
    case Administrator = 2;
    case Root = 3;
}
