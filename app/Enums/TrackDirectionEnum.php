<?php

namespace App\Enums;

enum TrackDirectionEnum: string
{
    case Eastbound = 'east';
    case Westbound = 'west';
    case Unknown = 'unknown';
}