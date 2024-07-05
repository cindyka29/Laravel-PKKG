<?php

namespace App\Models;

use App\Traits\HasActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * 
 *
 * @property string $id
 * @property string|null $activity_id
 * @property string|null $keterangan
 * @property string|null $tujuan
 * @property string $date
 * @property float $nominal
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Activity|null $activity
 * @property-read \App\Models\Images|null $image
 * @method static \Illuminate\Database\Eloquent\Builder|Kas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kas query()
 * @method static \Illuminate\Database\Eloquent\Builder|Kas whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kas whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kas whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kas whereTujuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kas whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kas whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kas whereNominal($value)
 * @mixin \Eloquent
 */
class Kas extends Model
{
    use HasFactory,HasActivity;

    protected $table = 'kas';
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $incrementing = false;

    public function image(): MorphOne
    {
        return $this->morphOne(Images::class,'imageable');
    }
}
