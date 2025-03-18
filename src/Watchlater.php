<?php

namespace Animelhd\AnimesWatchlater;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Animelhd\AnimesWatchlater\Events\Watchlatered;
use Animelhd\AnimesWatchlater\Events\Unwatchlatered;

/**
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $watchlaterer
 * @property \Illuminate\Database\Eloquent\Model $watchlaterable
 */
class Watchlater extends Model
{
    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => Watchlatered::class,
        'deleted' => Unwatchlatered::class,
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = \config('animeswatchlater.watchlaters_table');

        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();

        self::saving(function ($watchlater) {
            $userForeignKey = \config('animeswatchlater.user_foreign_key');
            $watchlater->{$userForeignKey} = $watchlater->{$userForeignKey} ?: auth()->id();

            if (\config('animeswatchlater.uuids')) {
                $watchlater->{$watchlater->getKeyName()} = $watchlater->{$watchlater->getKeyName()} ?: (string) Str::orderedUuid();
            }
        });
    }

    public function watchlaterable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\config('animeswatchlater.watchlaterer_model'), \config('animeswatchlater.user_foreign_key'));
    }

    public function watchlaterer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->user();
    }

    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('watchlaterable_type', app($type)->getMorphClass());
    }
}
