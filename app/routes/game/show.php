<?php

$app->get('/seasons/:seasonId/games/:gameId', $authenticated(), function($seasonId, $gameId) use($app) {

    /*
     * Get all games and also load their matches and players.
     * We order on the pivot id to make sure the order is correct.
     */
    $game = $app->game->with(['matches.players' => function($query) {
        $query->orderBy('pivot_id', 'asc');
    }, 'matches.sets'])->find($gameId);

//    echo count($game->matches[0]->sets) . '<br><br>';
//    die(print_r($game->matches[0]));


    $season = $app->season->find($seasonId);

    if ( ! $game || ! $season) {
        $app->notFound();
    }

    $app->render('game/show.twig', [
        'seasonId' => $seasonId,
        'game' => $game,

    ]);

})->name('game.show');