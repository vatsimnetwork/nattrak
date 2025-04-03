<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CtpBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'cid',
        'flight_level',
        'selcal',
        'destination',
        'track',
        'random_routeing',
    ];
}
