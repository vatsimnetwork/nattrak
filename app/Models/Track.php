<?php

namespace App\Models;

use App\Enums\TrackDirectionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Track
 *
 * @property int $id
 * @property string $identifier
 * @property int $active
 * @property string $last_routeing
 * @property string|null $valid_from
 * @property string|null $valid_to
 * @property string $last_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Track newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Track newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Track query()
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereLastActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereLastRouteing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereValidTo($value)
 * @method static Builder|Track active()
 * @property int $concorde
 * @method static Builder|Track concorde()
 * @method static Builder|Track whereConcorde($value)
 * @property string|null $flight_levels
 * @method static Builder|Track whereFlightLevels($value)
 * @property-read mixed $predominantly_odd_or_even
 * @property TrackDirectionEnum|null $direction
 * @method static Builder|Track whereDirection($value)
 * @property-read mixed $odd_or_even
 * @mixin \Eloquent
 */
class Track extends Model
{
    protected $fillable = [
        'identifier', 'active', 'last_routeing', 'valid_from', 'valid_to', 'last_active', 'concorde', 'flight_levels', 'direction'
    ];

    protected $casts = [
        'active' => 'bool',
        'valid_to' => 'datetime',
        'valid_from' => 'datetime',
        'last_active' => 'datetime',
        'flight_levels' => 'array',
        'direction' => TrackDirectionEnum::class,
    ];

    protected $appends = [
        'odd_or_even',
    ];

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeConcorde(Builder $query): Builder
    {
        return $query->where('concorde', true);
    }

    public function deactivate()
    {
        $this->valid_from = null;
        $this->valid_to = null;
        $this->active = false;
        $this->save();
    }

    protected function oddOrEven(): Attribute
    {
        return Attribute::make(
            get: fn () =>  $this->flight_levels ? $this->getPrimaryLevelType($this->flight_levels) : 'mixed',
        );
    }

    private function getPrimaryLevelType(array $flightLevels): string
    {
        $odd = 0;
        $even = 0;
        foreach ($flightLevels as $level) {
            if (($level / 100) % 2 === 0) {
                $even++;
            } else {
                $odd++;
            }
        }
        if ($odd > $even) {
            return 'odd';
        } elseif ($even > $odd) {
            return 'even';
        }
        return 'mixed';
    }
}
