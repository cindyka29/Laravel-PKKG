<?php

namespace App\Models;

use App\Traits\HasActivity;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $id
 * @property string|null $user_id
 * @property string|null $activity_id
 * @property int $is_paid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Activity|null $activity
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Iuran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Iuran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Iuran query()
 * @method static \Illuminate\Database\Eloquent\Builder|Iuran whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Iuran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Iuran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Iuran whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Iuran whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Iuran whereUserId($value)
 * @mixin \Eloquent
 */
class Iuran extends Model
{
    use HasFactory,HasUser,HasActivity;
    protected $table = 'iurans';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
}
