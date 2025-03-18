<?php

namespace Animelhd\AnimesWatchlater\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Database\Eloquent\Collection $watchlaterers
 * @property \Illuminate\Database\Eloquent\Collection $watchlaters
 */
trait Watchlaterable
{
    /**
     * @deprecated renamed to `hasBeenWatchlateredBy`, will be removed at 5.0
     */
    public function isWatchlateredBy(Model $user): bool
    {
        return $this->hasBeenWatchlateredBy($user);
    }

    public function hasFavoriter(Model $user): bool
    {
        return $this->hasBeenWatchlateredBy($user);
    }

    public function hasBeenWatchlateredBy(Model $user): bool
    {
        if (! \is_a($user, config('animeswatchlater.watchlaterer_model'))) {
            return false;
        }

        if ($this->relationLoaded('watchlaterers')) {
            return $this->watchlaterers->contains($user);
        }

        return ($this->relationLoaded('watchlaters') ? $this->watchlaters : $this->watchlaters())
            ->where(\config('animeswatchlater.user_foreign_key'), $user->getKey())->count() > 0;
    }

    public function watchlaters(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(config('animeswatchlater.watchlater_model'), 'watchlaterable');
    }

    public function watchlaterers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            config('animeswatchlater.watchlaterer_model'),
            config('animeswatchlater.watchlaters_table'),
            'watchlaterable_id',
            config('animeswatchlater.user_foreign_key')
        )
            ->where('watchlaterable_type', $this->getMorphClass());
    }
}
