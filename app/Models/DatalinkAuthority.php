<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DatalinkAuthority
 *
 * @property string $id
 * @property string $name
 * @property string $prefix
 * @property bool $auto_acknowledge_participant
 * @property bool $valid_rcl_target
 * @property bool $system
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority query()
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority whereAutoAcknowledgeParticipant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority whereSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatalinkAuthority whereValidRclTarget($value)
 * @mixin \Eloquent
 */
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
