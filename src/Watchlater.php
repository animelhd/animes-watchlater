<?php

namespace Animelhd\AnimesWatchlater;

use Illuminate\Database\Eloquent\Model;
use App\Models\Anime;
use Animelhd\AnimesWatchlater\Events\Watchlatered;
use Animelhd\AnimesWatchlater\Events\Unwatchlatered;

class Watchlater extends Model
{
    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => Watchlatered::class,
        'deleted' => Unwatchlatered::class,
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('animeswatchlater.watchlaters_table');
        parent::__construct($attributes);
    }

    public function anime()
    {
        return $this->belongsTo(config('animeswatchlater.watchlaterable_model'), config('animeswatchlater.anime_foreign_key'));
    }

    public function user()
    {
        return $this->belongsTo(config('animeswatchlater.user_model'), config('animeswatchlater.user_foreign_key'));
    }
}
