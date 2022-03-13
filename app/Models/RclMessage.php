<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\RclMessage
 *
 * @property int $id
 * @property int $vatsim_account_id
 * @property string $callsign
 * @property string $destination
 * @property string $flight_level
 * @property string $mach
 * @property int|null $track_id
 * @property string|null $random_routeing
 * @property string $entry_fix
 * @property string $entry_time
 * @property string|null $tmi
 * @property string $request_time
 * @property string|null $free_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $clx_message_id
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereCallsign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereClxMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereEntryFix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereEntryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereFlightLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereFreeText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereMach($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereRandomRouteing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereRequestTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereTmi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereTrackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereVatsimAccountId($value)
 * @mixin \Eloquent
 */
class RclMessage extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('rcl');
    }

    protected $fillable = [
        'vatsim_account_id', 'callsign', 'destination', 'flight_level', 'mach', 'track_id', 'random_routeing', 'entry_fix', 'entry_time', 'tmi', 'request_time', 'free_text'
    ];

    public function vatsimAccount()
    {
        return $this->belongsTo(VatsimAccount::class);
    }

    public function clxMessage()
    {
        return $this->hasOne(ClxMessage::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}
