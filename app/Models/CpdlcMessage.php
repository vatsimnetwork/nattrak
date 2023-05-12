<?php

namespace App\Models;

use App\Enums\DatalinkAuthorities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/**
 * App\Models\CpdlcMessage
 *
 * @property int $id
 * @property int $controller_id
 * @property int $pilot_id
 * @property string $pilot_callsign
 * @property DatalinkAuthorities $datalink_authority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\VatsimAccount|null $controller
 * @property-read \App\Models\VatsimAccount|null $pilot
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CpdlcMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CpdlcMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CpdlcMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|CpdlcMessage whereControllerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CpdlcMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CpdlcMessage whereDatalinkAuthority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CpdlcMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CpdlcMessage wherePilotCallsign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CpdlcMessage wherePilotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CpdlcMessage whereUpdatedAt($value)
 *
 * @property string $message
 * @property string|null $caption
 *
 * @method static Builder|CpdlcMessage whereCaption($value)
 * @method static Builder|CpdlcMessage whereMessage($value)
 * @mixin \Eloquent
 */
class CpdlcMessage extends Model
{
    use Prunable;

    /**
     * Set the pruning options.
     *
     * @return Builder
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subHours(24));
    }

    protected $fillable = [
        'pilot_id', 'pilot_callsign', 'datalink_authority', 'message', 'caption',
    ];

    protected $casts = [
        'datalink_authority' => DatalinkAuthorities::class,
    ];

    public function pilot()
    {
        return $this->belongsTo(VatsimAccount::class, 'pilot_id');
    }

    public function toMessageHistoryFormat(): array
    {
        return [
            'id' => $this->id,
            'datalink_authority' => [
                'id' => $this->datalink_authority->name,
                'description' => $this->datalink_authority->description(),
            ],
            'message' => $this->message,
            'caption' => $this->caption,
            'created_at' => $this->created_at,
        ];
    }
}
