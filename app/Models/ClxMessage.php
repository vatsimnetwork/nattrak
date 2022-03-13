<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
 * @mixin \Eloquent
 */
class ClxMessage extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('clx');
    }
}
