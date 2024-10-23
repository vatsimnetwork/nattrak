<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatalinkAuthority extends Model
{
    protected $fillable = [
        'id',
        'name',
        'prefix',
        'auto_acknowledge_participant',
        'valid_rcl_target',
        'system',
    ];

    protected $casts = [
        'auto_acknowledge_participant' => 'boolean',
        'valid_rcl_target' => 'boolean',
        'system' => 'boolean',
    ];

    protected $keyType = 'string';
}
