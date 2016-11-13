<?php

$app->get('/players/stats/:seasonId', $authenticated(), function($seasonId) use($app) {

    if ($seasonId !== 'all') {
        /*
         * Retrieve the season and eager load games, matches and players.
         */
        $season = $app->season->find($seasonId);

        /*
         * Does the season exists.
         */
        if ( ! $season) {
            $app->notFound();
        }
    }

    if ($seasonId === 'all') {
        $players = $app->player
            ->with(['matches' => function($q) use($seasonId) {
                $q->with('sets.match.game', 'setsCount', 'game', 'players');
            }])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }
    else {
        $players = $app->player
            ->whereHas('matches.game', function($q) use($seasonId) {
                $q->where('season_id', $seasonId);
            })
            ->with(['matches' => function($q) use($seasonId) {
                $q->whereHas('game', function($q) use($seasonId) {
                    $q->where('season_id', $seasonId);
                })
                ->with('sets.match.game', 'setsCount', 'game', 'players');
            }])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    $app->render('player/stats.twig', [
        'seasonId' => $seasonId,
        'players' => $players
    ]);

})->name('player.stats');