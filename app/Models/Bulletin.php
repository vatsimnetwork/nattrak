<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Bulletin
 *
 * @property int $id
 * @property string $title
 * @property string|null $subtitle
 * @property string $content
 * @property string|null $action_url
 * @property int $alert_controllers
 * @property int $alert_pilots
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\BulletinFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin whereActionUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin whereAlertControllers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin whereAlertPilots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bulletin withoutTrashed()
 * @mixin \Eloquent
 */
class Bulletin extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'action_url',
        'alert_controllers',
        'alert_pilots',
    ];
}
