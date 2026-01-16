<?php

namespace Gottvergessen\Logger\Observers;

use Illuminate\Database\Eloquent\Model;
use Gottvergessen\Logger\Services\ActivityTracker;

class TrackableObserver
{
    public function created(Model $model): void
    {
        $this->track($model, 'created');
    }

    public function updated(Model $model): void
    {
        $this->track($model, 'updated');
    }

    public function deleted(Model $model): void
    {
        $this->track($model, 'deleted');
    }

    protected function track(Model $model, string $event): void
    {
        if (! method_exists($model, 'shouldTrackEvent')) {
            return;
        }

        if (! $model->shouldTrackEvent($event)) {
            return;
        }

        app(ActivityTracker::class)->track($model, $event);
    }
}
