<?php

namespace App\Models;

use App\Enums\AccessLevelEnum;
use App\Enums\DatalinkAuthorities;
use App\Services\VatsimDataService;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\VatsimAccount
 *
 * @property int $id
 * @property string|null $given_name
 * @property string|null $surname
 * @property int $rating_int
 * @property AccessLevelEnum $access_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|VatsimAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VatsimAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VatsimAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|VatsimAccount whereAccessLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VatsimAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VatsimAccount whereGivenName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VatsimAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VatsimAccount whereRatingInt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VatsimAccount whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VatsimAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read mixed $full_name
 * @property-read \App\Enums\DatalinkAuthorities|null $active_datalink_authority
 */
class VatsimAccount extends Authenticatable
{
    use LogsActivity;

    /**
     * @var string[]
     */
    protected $fillable = ['id', 'given_name', 'surname', 'rating_int', 'access_level'];

    /**
     * @var string[]
     */
    protected $casts = [
        'access_level' => AccessLevelEnum::class
    ];

    /**
     * Activity log options.
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['access_level']);
    }

    public function getFullNameAttribute()
    {
        return $this->given_name . ' ' . $this->surname;
    }

    public function getActiveDatalinkAuthorityAttribute(): ?DatalinkAuthorities
    {
        $dataService = new VatsimDataService();
        if ($this->can('activeController') && $dataService->isActiveOceanicController($this)) {
            return $dataService->getActiveControllerAuthority($this);
        } else {
            return null;
        }
    }
}
