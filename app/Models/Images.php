<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 
 *
 * @property-read Model|\Eloquent $imageable
 * @method static \Illuminate\Database\Eloquent\Builder|Images newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Images newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Images query()
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string|null $imageable_type
 * @property string|null $imageable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Images whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Images whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Images whereImageableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Images whereImageableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Images whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Images whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Images whereUrl($value)
 * @mixin \Eloquent
 */
class Images extends Model
{
    use HasFactory;
    protected $table = 'images';

    public function imageable(): MorphTo
    {
        return $this->morphTo('imageable');
    }
}
