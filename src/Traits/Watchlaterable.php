<?php

namespace Animelhd\AnimesWatchlater\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Animelhd\AnimesWatchlater\Watchlater;

trait Watchlaterable
{
    public function watchlaters(): HasMany
    {
        return $this->hasMany(config('animeswatchlater.watchlater_model'), config('animeswatchlater.anime_foreign_key'));
    }
}