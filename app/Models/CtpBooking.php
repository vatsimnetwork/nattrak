<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CtpBooking
 *
 * @property int $id
 * @property string $cid
 * @property string|null $flight_level
 * @property string|null $selcal
 * @property string $destination
 * @property string|null $track
 * @property string|null $random_routeing
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CtpBookingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking query()
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking whereCid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking whereFlightLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking whereRandomRouteing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking whereSelcal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking whereTrack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CtpBooking whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
