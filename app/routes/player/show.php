<?php

$app->get('/players/:id', $authenticated(), function($id) use($app) {

    $player = $app->player->with('matches', 'matches.sets.match.game', 'matches.game', 'matches.setsCount', 'matches.players')->find($id);

    if ( ! $player) {
        $app->notFound();
    }

    $players = $app->player
        ->has('matches')
        ->with(['matches' => function($q) {
            $q->with('sets.match.game', 'setsCount', 'game', 'players');
        }])
        ->where('id', '!=', $player->id)
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();

    if( ! $player) {
        $app->notFound();
    }

    $app->render('player/show.twig', [
        'player' => $player,
        'players' => $players
    ]);

})->name('player.show');

