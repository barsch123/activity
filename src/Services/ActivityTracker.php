<?php

namespace Gottvergessen\Logger\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Gottvergessen\Logger\Models\Activity;
use Gottvergessen\Logger\Support\ActivityContext;

class ActivityTracker
{
    public static function track(Model $model, string $event, ?array $attributes = null): void
    {
        if (! static::shouldTrack($model, $event)) {
            return;
        }
        $user = Auth::user();

        if ($user) {
            ActivityContext::setCauser(
                get_class($user),
                $user->getAuthIdentifier(),
            );
        }
        Activity::create([
            'event'        => $event,
            'action'       => static::action($model, $event),
            'log'          => static::log($model),
            'description'  => static::description($model, $event),

            'subject_type' => get_class($model),
            'subject_id'   => $model->getKey(),

            'causer_type'  => ActivityContext::causerType(),
            'causer_id'    => ActivityContext::causerId(),
            'properties'   => static::resolveProperties($model, $event, $attributes),
            'meta'         => ActivityContext::addMeta([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'method' => request()->method(),
                'host' => request()->httpHost(),
            ]),

            'origin'       => ActivityContext::origin(),
            'batch_id'     => static::batchId(),
        ]);
    }

    /* -------------------------------- */
    /* Core decisions                   */
    /* -------------------------------- */

    protected static function action(Model $model, string $event): ?string
    {
        // Model-defined semantic action
        if (method_exists($model, 'activityAction')) {
            return $model->activityAction($event);
        }

        return null;
    }

    protected static function properties(
        Model $model,
        string $event,
        ?array $attributes
    ): array {
        return static::resolveProperties($model, $event, $attributes);
    }


    protected static function log(Model $model): string
    {
        if (method_exists($model, 'activityLog')) {
            return (string) $model->activityLog();
        }

        return (string) config('logger.default_log', 'default');
    }




    protected static function shouldTrack(Model $model, string $event): bool
    {
        $events = config('logger.events', []);

        $modelKey = get_class($model);

        if (! isset($events[$modelKey])) {
            return true;
        }

        return in_array($event, $events[$modelKey], true);
    }

    /* -------------------------------- */
    /* Data builders                    */
    /* -------------------------------- */

    protected static function resolveProperties(
        Model $model,
        string $event,
        ?array $attributes
    ): array {
        return match ($event) {
            'created' => $attributes ?? $model->getAttributes(),
            'updated' => static::resolveChanges($model),
            default   => [],
        };
    }

    protected static function resolveChanges(Model $model): array
    {
        return collect($model->getChanges())
            ->reject(fn($_, $key) => in_array($key, config('logger.ignore', [])))
            ->map(fn($value, $key) => [
                'old' => $model->getOriginal($key),
                'new' => $value,
            ])
            ->toArray();
    }

    /* -------------------------------- */
    /* Metadata                         */
    /* -------------------------------- */

    protected static function description(Model $model, string $event): string
    {
        return Str::headline(class_basename($model)) . " {$event}";
    }

    protected static function batchId(): ?string
    {
        return static::explicitBatch()
            ?? static::requestBatch()
            ?? static::autoBatch();
    }

    protected static function explicitBatch(): ?string
    {
        return app()->bound('logger.batch')
            ? app('logger.batch')
            : null;
    }
    protected static function requestBatch(): ?string
    {
        return ActivityContext::batchId();
    }

    protected static function autoBatch(): ?string
    {
        return config('logger.auto_batch', false)
            ? (string) Str::uuid()
            : null;
    }
}
