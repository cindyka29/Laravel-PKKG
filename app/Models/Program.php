<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @method static \Illuminate\Database\Eloquent\Builder|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program query()
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereUpdatedAt($value)
 * @property-read \App\Models\Images|null $image
 * @property string|null $note
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereNote($value)
 * @mixin \Eloquent
 */
class Program extends Model
{
    use HasFactory;
    protected $table = "programs";
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $incrementing = false;

    public function image(): MorphOne
    {
        return $this->morphOne(Images::class,'imageable');
    }

    public function activities() : HasMany
    {
        return $this->hasMany(Activity::class,"program_id","id");
    }
}
