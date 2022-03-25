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
 * @property string|null $max_flight_level
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClxMessage[] $clxMessages
 * @property-read int|null $clx_messages_count
 * @property-read \App\Models\Track|null $track
 * @property-read \App\Models\VatsimAccount $vatsimAccount
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage whereMaxFlightLevel($value)
 * @property-read mixed $data_link_message
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage cleared()
 * @method static \Illuminate\Database\Eloquent\Builder|RclMessage pending()
 */
class RclMessage extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('rcl');
    }

    protected $fillable = [
        'vatsim_account_id', 'callsign', 'destination', 'flight_level', 'max_flight_level', 'mach', 'track_id', 'random_routeing', 'entry_fix', 'entry_time', 'tmi', 'request_time', 'free_text'
    ];

    protected $dates = [
        'request_time'
    ];

    public function scopePending($query)
    {
        return $query->where('clx_message_id', null);
    }

    public function scopeCleared($query)
    {
        return $query->where('clx_message_id', '!=', null);
    }

    public function vatsimAccount()
    {
        return $this->belongsTo(VatsimAccount::class);
    }

    public function clxMessages()
    {
        return $this->hasMany(ClxMessage::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function getDataLinkMessageAttribute()
    {
        if ($this->track) {
            return "{$this->callsign} REQ CLRNCE {$this->destination} VIA {$this->entry_fix}/{$this->entry_time} TRACK {$this->track->identifier} F{$this->flight_level} M{$this->mach} MAX F{$this->max_flight_level} TMI {$this->tmi}";
        } else {
            return "{$this->callsign} REQ CLRNCE {$this->destination} VIA {$this->entry_fix}/{$this->entry_time} {$this->random_routeing} F{$this->flight_level} M{$this->mach} MAX F{$this->max_flight_level} TMI {$this->tmi}";
        }
    }
}
