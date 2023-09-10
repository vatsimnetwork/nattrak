<?php

namespace App\Models;

use App\Enums\DatalinkAuthorities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\ClxMessage
 *
 * @property int $id
 * @property int $vatsim_account_id
 * @property int $rcl_message_id
 * @property string $flight_level
 * @property string $mach
 * @property int|null $track_id
 * @property string|null $random_routeing
 * @property string $entry_fix
 * @property string|null $entry_time_restriction
 * @property string|null $free_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereEntryFix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereEntryTimeRestriction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereFlightLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereFreeText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereMach($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereRandomRouteing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereRclMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereTrackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereVatsimAccountId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read array $data_link_message
 * @property-read \App\Models\RclMessage $rclMessage
 * @property-read \App\Models\Track|null $track
 * @property-read mixed $simple_message
 * @property-read \App\Models\VatsimAccount $vatsimAccount
 * @property DatalinkAuthorities $datalink_authority
 * @method static \Illuminate\Database\Eloquent\Builder|ClxMessage whereDatalinkAuthority($value)
 * @property string $simple_datalink_message
 * @property mixed $datalink_message
 * @method static Builder|ClxMessage whereDatalinkMessage($value)
 * @method static Builder|ClxMessage whereSimpleDatalinkMessage($value)
 * @property string|null $upper_flight_level
 * @method static Builder|ClxMessage whereUpperFlightLevel($value)
 * @property-read bool $routeing_changed
 * @property-read string|null $raw_entry_time_restriction
 * @method static Builder|ClxMessage whereRawEntryTimeRestriction($value)
 * @property int $overwritten
 * @property int|null $overwritten_by_clx_message_id
 * @method static Builder|ClxMessage whereOverwritten($value)
 * @method static Builder|ClxMessage whereOverwrittenByClxMessageId($value)
 * @property int $is_concorde
 * @method static Builder|ClxMessage whereIsConcorde($value)
 * @mixin \Eloquent
 */
class ClxMessage extends Model
{
    use LogsActivity;
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

    /**
     * The mass assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'vatsim_account_id', 'rcl_message_id', 'flight_level', 'mach', 'track_id', 'random_routeing', 'entry_fix', 'entry_time_restriction', 'free_text', 'datalink_authority', 'simple_datalink_message', 'datalink_message', 'upper_flight_level', 'raw_entry_time_restriction', 'overwritten_by_clx_message_id', 'overwritten', 'is_concorde',
    ];

    /**
     * Activity log options
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('clx');
    }

    /**
     * Attributes casted as date/times
     *
     * @var string[]
     */
    protected $casts = [
        'datalink_authority' => DatalinkAuthorities::class,
        'datalink_message' => 'array',
    ];

    /**
     * Returns the RCL Message this was in reply to.
     *
     * @return BelongsTo
     */
    public function rclMessage(): BelongsTo
    {
        return $this->belongsTo(RclMessage::class);
    }

    /**
     * Returns the track the CLX was for.
     *
     * @return BelongsTo
     */
    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    /**
     * Returns the track the CLX was for.
     *
     * @return BelongsTo
     */
    public function vatsimAccount(): BelongsTo
    {
        return $this->belongsTo(VatsimAccount::class);
    }

    /**
     * @return string|null
     */
    public function formatEntryTimeRestriction(): ?string
    {
        if (! $this->entry_time_restriction) {
            return null;
        }

        return match (substr($this->entry_time_restriction, 0, 1)) {
            '<' => 'BEFORE '.substr($this->entry_time_restriction, 1, 4),
            '=' => 'AT '.substr($this->entry_time_restriction, 1, 4),
            '>' => 'AFTER '.substr($this->entry_time_restriction, 1, 4),
        };
    }

    /**
     * @return bool
     */
    public function getRouteingChangedAttribute(): bool
    {
        if ($this->rclMessage->track) {
            return ! $this->track;
        } elseif ($this->rclMessage->random_routeing) {
            return ! $this->random_routeing;
        } else {
            return false;
        }
    }

    public function toMessageHistoryFormat(): array
    {
        return [
            'id' => $this->id,
            'rcl_message_id' => $this->rcl_message_id,
            'simple_datalink_message' => $this->simple_datalink_message,
            'datalink_message' => $this->datalink_message,
            'datalink_authority' => [
                'id' => $this->datalink_authority->name,
                'description' => $this->datalink_authority->description(),
            ],
            'created_at' => $this->created_at,
        ];
    }
}
