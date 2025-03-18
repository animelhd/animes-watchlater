<?php

namespace Animelhd\AnimesWatchlater\Events;

use Illuminate\Database\Eloquent\Model;

class Event
{
    public Model $watchlater;

    public function __construct(Model $watchlater)
    {
        $this->watchlater = $watchlater;
    }
}
