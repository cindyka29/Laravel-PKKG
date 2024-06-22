<?php

namespace App\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasActivity
{
    public function activity() : BelongsTo
    {
        return $this->belongsTo(Activity::class,'activity_id','id');
    }
}
