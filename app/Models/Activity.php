<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property string $id
 * @property string $name
 * @property string|null $note
 * @property string $date
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Images> $documentations
 * @property-read int|null $documentations_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity query()
 * @property string|null $program_id
 * @property-read \App\Models\Program|null $program
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereProgramId($value)
 * @mixin \Eloquent
 */
class Activity extends Model
{
    use HasFactory;
    protected $table = 'activities';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function documentations(): MorphMany
    {
        return $this->morphMany(Images::class,'imageable');
    }

    public function program() : BelongsTo
    {
        return $this->belongsTo(Program::class,"program_id","id");
    }

    public function kas() : HasMany
    {
        return $this->HasMany(Kas::class,"activity_id","id");
    }
}
