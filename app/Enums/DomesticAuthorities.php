<?php

namespace App\Enums;

enum DomesticAuthorities: string
{
    case EISN = 'EISN';
    case LPPC = 'LPPC';
    case LFRR = 'LFRR';
    case EGTT = 'EGTT';
    case CZQMD = 'CZQM';
    case CZQXD = 'CZQX';
    case ATL = 'ATL';
    case UNKN = 'UNKN';

    public function description(): string
    {
        return match ($this) {
            DomesticAuthorities::EISN => 'Shannon',
            DomesticAuthorities::LPPC => 'Lisbon',
            DomesticAuthorities::LFRR => 'Brest',
            DomesticAuthorities::EGTT => 'London',
            DomesticAuthorities::CZQMD => 'Moncton',
            DomesticAuthorities::CZQXD => 'Moncton/Gander',
            DomesticAuthorities::ATL => 'ATL',
            DomesticAuthorities::UNKN => 'Unknown',
            default => 'Unknown',
        };
    }
}
