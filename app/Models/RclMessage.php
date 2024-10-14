<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Query\Builder|RclMessage onlyTrashed()
 * @method static Builder|RclMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RclMessage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RclMessage withoutTrashed()
 * @method static \Database\Factories\RclMessageFactory factory(...$parameters)
 * @method static Builder|RclMessage requestedRandomRouteing()
 * @method static Builder|RclMessage requestedTrack(\App\Models\Track $track)
 * @property int $atc_rejected
 * @method static Builder|RclMessage whereAtcRejected($value)
 * @property int $edit_lock
 * @property \Illuminate\Support\Carbon|null $edit_lock_time
 * @property int|null $edit_lock_vatsim_account_id
 * @property-read \App\Models\VatsimAccount|null $editLockVatsimAccount
 * @method static Builder|RclMessage whereEditLock($value)
 * @method static Builder|RclMessage whereEditLockTime($value)
 * @method static Builder|RclMessage whereEditLockVatsimAccountId($value)
 * @property string|null $upper_flight_level
 * @property int $is_concorde
 * @method static Builder|RclMessage whereIsConcorde($value)
 * @method static Builder|RclMessage whereUpperFlightLevel($value)
 * @property-read \App\Models\ClxMessage|null $latestClxMessage
 * @property string|null $previous_entry_time
 * @property int $new_entry_time
 * @property mixed|null $previous_clx_message
 * @method static Builder|RclMessage whereNewEntryTime($value)
 * @method static Builder|RclMessage wherePreviousClxMessage($value)
 * @method static Builder|RclMessage wherePreviousEntryTime($value)
 * @property \Illuminate\Support\Carbon|null $new_entry_time_notified_at
 * @method static Builder|RclMessage whereNewEntryTimeNotifiedAt($value)
 * @property int $re_request
 * @property-read string $route_identifier
 * @method static Builder|RclMessage whereReRequest($value)
 * @property int $is_acknowledged
 * @property string|null $acknowledged_at
 * @method static Builder|RclMessage whereAcknowledgedAt($value)
 * @method static Builder|RclMessage whereIsAcknowledged($value)
 * @mixin \Eloquent
 */
class RclMessage extends Model
{
    use LogsActivity;

    //use SoftDeletes;
    use HasFactory;
    use Prunable;

    /**
     * Activity log options
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('rcl');
    }

    /**
     * Set the pruning options.
     *
     * @return Builder
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subHours(24));
    }

    /**
     * Mass assignable attributes.
     *
     * @var string[]
     */
    protected $fillable = [
        'vatsim_account_id', 'callsign', 'destination', 'flight_level', 'max_flight_level', 'mach', 'track_id', 'random_routeing', 'entry_fix', 'entry_time', 'tmi', 'request_time', 'free_text', 'atc_rejected', 'upper_flight_level', 'is_concorde', 'previous_entry_time', 'new_entry_time', 'previous_clx_message', 'new_entry_time_notified_at', 'is_acknowledged', 'acknowledged_at'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'route_identifier'
    ];

    /**
     * Attributes casted as date/times
     *
     * @var string[]
     */
    protected $casts = [
        'request_time' => 'datetime',
        'edit_lock_time' => 'datetime',
        'new_entry_time_notified_at' => 'datetime',
        'previous_clx_message' => 'array'
    ];

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('clx_message_id', null);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeCleared(Builder $query): Builder
    {
        return $query->whereNotNull('clx_message_id');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotAcknowledged(Builder $query): Builder
    {
        return $query->whereNot('is_acknowledged');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeAcknowledged(Builder $query): Builder
    {
        return $query->where('is_acknowledged');
    }

    /**
     * Scope to messages for a specific track.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeRequestedTrack(Builder $query, Track $track): Builder
    {
        return $query->where('track_id', $track->id)->where('random_routeing', null);
    }

    /**
     * Scope to messages for a random routeing.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeRequestedRandomRouteing(Builder $query): Builder
    {
        return $query->where('random_routeing', '!=', null)->where('track_id', null);
    }

    /**
     * Returns the VATSIM account this RCL message was transmitted by
     *
     * @return BelongsTo
     */
    public function vatsimAccount(): BelongsTo
    {
        return $this->belongsTo(VatsimAccount::class);
    }

    /**
     * Returns the CLX messages in reply to this message.
     *
     * @return HasMany
     */
    public function clxMessages(): HasMany
    {
        return $this->hasMany(ClxMessage::class);
    }

    public function latestClxMessage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ClxMessage::class)->latest();
    }

    /**
     * Returns the track this was a request for.
     *
     * @return BelongsTo
     */
    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    /**
     * Returns the formatted datalink message string.
     *
     * @return string
     */
    public function getDataLinkMessageAttribute(): string
    {
        if ($this->is_concorde) {
            if ($this->track) {
                return "{$this->callsign} REQ CONC CLRNCE {$this->destination} VIA {$this->entry_fix}/{$this->entry_time} CONC TRACK {$this->track->identifier} BLOCK LOWER F{$this->flight_level} UPPER F{$this->upper_flight_level} M{$this->mach} TMI {$this->tmi}";
            } else {
                return "{$this->callsign} REQ CONC CLRNCE {$this->destination} VIA {$this->entry_fix}/{$this->entry_time} {$this->random_routeing} BLOCK LOWER F{$this->flight_level} UPPER F{$this->upper_flight_level} M{$this->mach} TMI {$this->tmi}";
            }
        } else {
            if ($this->track) {
                return "{$this->callsign} REQ CLRNCE {$this->destination} VIA {$this->entry_fix}/{$this->entry_time} TRACK {$this->track->identifier} F{$this->flight_level} M{$this->mach} MAX F{$this->max_flight_level} TMI {$this->tmi}";
            } else {
                return "{$this->callsign} REQ CLRNCE {$this->destination} VIA {$this->entry_fix}/{$this->entry_time} {$this->random_routeing} F{$this->flight_level} M{$this->mach} MAX F{$this->max_flight_level} TMI {$this->tmi}";
            }
        }
    }

    public function isEditLocked(): bool
    {
        return $this->edit_lock && $this->edit_lock_time->diffInMinutes(now()) < 5;
    }

    public function editLockVatsimAccount()
    {
        return $this->belongsTo(VatsimAccount::class, 'edit_lock_vatsim_account_id');
    }

    protected function routeIdentifier(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->track ? $this->track->id : 'RR'
        );
    }
}
