<?php

namespace Gottvergessen\Logger\Traits;

use Gottvergessen\Logger\Models\Activity;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasActivities
{
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
