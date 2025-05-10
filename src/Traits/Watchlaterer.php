<?php

namespace Animelhd\AnimesWatchlater\Traits;

use Animelhd\AnimesWatchlater\Watchlater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\Anime;

trait Watchlaterer
{
	public function watchlater(Anime $anime): void
    {
        if (! $this->hasWatchlatered($anime)) {
            $this->watchlaters()->create([
                'anime_id' => $anime->getKey(),
            ]);
        }
    }

    public function unwatchlater(Anime $anime): void
    {
        $this->watchlaters()
            ->where('anime_id', $anime->getKey())
            ->delete();
    }

    public function toggleWatchlater(Anime $anime): void
    {
        $this->hasWatchlatered($anime)
            ? $this->unwatchlater($anime)
            : $this->watchlater($anime);
    }

    public function hasWatchlatered(Anime $anime): bool
    {
        return $this->watchlaters()
            ->where('anime_id', $anime->getKey())
            ->exists();
    }

    public function watchlaters()
    {
        return $this->hasMany(Watchlater::class, config('animeswatchlater.user_foreign_key'));
    }
}
