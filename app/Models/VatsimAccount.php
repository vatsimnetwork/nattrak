<?php

namespace App\Models;

use App\Enums\AccessLevelEnum;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
 */
class VatsimAccount extends Authenticatable
{
    protected $fillable = ['id', 'given_name', 'surname', 'rating_int', 'access_level'];

    protected $casts = [
        'access_level' => AccessLevelEnum::class
    ];


}
