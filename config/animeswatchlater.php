<?php

return [
    /**
     * Use uuid as primary key.
     */
    'uuids' => false,

    /*
     * User tables foreign key name.
     */
    'user_foreign_key' => 'user_id',
	
    /*
     * Anime tables foreign key name.
     */
    'anime_foreign_key' => 'anime_id',			

    /*
     * Table name for watchlaters records.
     */
    'watchlaters_table' => 'watchlaters',

    /*
     * Model name for watchlater record.
     */
    'watchlater_model' => Animelhd\AnimesWatchlater\Watchlater::class,

	/*
     * Model name for watchlaterable record.
     */
    'watchlaterable_model' => App\Models\Anime::class,

     /*
     * Model name for watchlaterer model.
     */
    'watchlaterer_model' => App\Models\User::class,
];
