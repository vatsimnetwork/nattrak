<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
 * @mixin \Eloquent
 */
class Track extends Model
{
    protected $fillable = [
        'identifier', 'active', 'last_routeing', 'valid_from', 'valid_to', 'last_active', 'concorde'
    ];

    protected $dates = [
        'valid_to', 'valid_from', 'last_active'
    ];

    protected $casts = [
        'active' => 'bool'
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * @param Builder $query
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
}
