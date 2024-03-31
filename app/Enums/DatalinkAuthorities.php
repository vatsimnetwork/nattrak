<?php

namespace App\Enums;

enum DatalinkAuthorities: string
{
    case CZQX = 'CZQO';
    case EGGX = 'EGGX';
    case BIRD = 'BIRD';
    case KZNY = 'NY';
    case LPPO = 'LPPO';
    case NAT = 'NAT';
    case TTZO = 'TTZO';
    case CZQXD = 'CZQX';
    case CZQMD = 'CZQM';
    case SYS = 'SYST';
    case OCEN = 'OCEN';

    public function description(): string
    {
        return match ($this) {
            DatalinkAuthorities::NAT => 'North Atlantic Bandbox',
            DatalinkAuthorities::BIRD => 'Reykjavik',
            DatalinkAuthorities::TTZO => 'Piarco',
            DatalinkAuthorities::LPPO => 'Santa Maria',
            DatalinkAuthorities::CZQX => 'Gander',
            DatalinkAuthorities::EGGX => 'Shanwick',
            DatalinkAuthorities::KZNY => 'New York',
            DatalinkAuthorities::CZQMD => 'Moncton (Domestic)',
            DatalinkAuthorities::CZQXD => 'Gander (Domestic)',
            DatalinkAuthorities::SYS => 'natTrak System',
            DatalinkAuthorities::OCEN => 'Oceanic Controller',
            default => 'N/A'
        };
    }
}
