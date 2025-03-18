<?php

namespace Animelhd\AnimesWatchlater\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

/**
 * @property \Illuminate\Database\Eloquent\Collection $watchlaters
 */
trait Watchlaterer
{
    public function watchlater(Model $object): void
    {
        /* @var \Animelhd\AnimesWatchlater\Traits\Watchlaterable $object */
        if (! $this->hasWatchlatered($object)) {
            $watchlater = app(config('animeswatchlater.watchlater_model'));
            $watchlater->{config('animeswatchlater.user_foreign_key')} = $this->getKey();

            $object->watchlaters()->save($watchlater);
        }
    }

    public function unwatchlater(Model $object): void
    {
        /* @var \Animelhd\AnimesWatchlater\Traits\Watchlaterable $object */
        $relation = $object->watchlaters()
            ->where('watchlaterable_id', $object->getKey())
            ->where('watchlaterable_type', $object->getMorphClass())
            ->where(config('animeswatchlater.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            $relation->delete();
        }
    }

    public function toggleWatchlater(Model $object): void
    {
        $this->hasWatchlatered($object) ? $this->unwatchlater($object) : $this->watchlater($object);
    }

    public function hasWatchlatered(Model $object): bool
    {
        return ($this->relationLoaded('watchlaters') ? $this->watchlaters : $this->watchlaters())
            ->where('watchlaterable_id', $object->getKey())
            ->where('watchlaterable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function watchlaters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(config('animeswatchlater.watchlater_model'), config('animeswatchlater.user_foreign_key'), $this->getKeyName());
    }

    public function attachWatchlaterStatus(&$watchlaterables, ?callable $resolver)
    {
        $watchlaters = $this->watchlaters()->get()->keyBy(function ($item) {
            return \sprintf('%s-%s', $item->watchlaterable_type, $item->watchlaterable_id);
        });

        $attachStatus = function ($watchlaterable) use ($watchlaters, $resolver) {
            $resolver = $resolver ?? fn ($m) => $m;
            $watchlaterable = $resolver($watchlaterable);

            if (\in_array(Watchlaterable::class, \class_uses($watchlaterable))) {
                $key = \sprintf('%s-%s', $watchlaterable->getMorphClass(), $watchlaterable->getKey());
                $watchlaterable->setAttribute('has_watchlatered', $watchlaters->has($key));
            }

            return $watchlaterable;
        };

        switch (true) {
            case $watchlaterables instanceof Model:
                return $attachStatus($watchlaterables);
            case $watchlaterables instanceof Collection:
                return $watchlaterables->each($attachStatus);
            case $watchlaterables instanceof LazyCollection:
                return $watchlaterables = $watchlaterables->map($attachStatus);
            case $watchlaterables instanceof AbstractPaginator:
            case $watchlaterables instanceof AbstractCursorPaginator:
                return $watchlaterables->through($attachStatus);
            case $watchlaterables instanceof Paginator:
                // custom paginator will return a collection
                return collect($watchlaterables->items())->transform($attachStatus);
            case \is_array($watchlaterables):
                return \collect($watchlaterables)->transform($attachStatus);
            default:
                throw new \InvalidArgumentException('Invalid argument type.');
        }
    }

    public function getWatchlaterItems(string $model)
    {
        return app($model)->whereHas(
            'watchlaterers',
            function ($q) {
                return $q->where(config('animeswatchlater.user_foreign_key'), $this->getKey());
            }
        );
    }
}
