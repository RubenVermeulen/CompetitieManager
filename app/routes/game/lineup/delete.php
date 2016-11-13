<?php

$app->delete('/seasons/:seasonId/games/:gameId/lineup', $admin(), function($seasonId, $gameId) use($app) {

    $app->match->where('game_id', $gameId)->delete();

    $app->flash('global', 'De lineup is verwijderd.');
    $app->response->redirect($app->urlFor('game.show', [
        'seasonId' => $seasonId,
        'gameId' => $gameId
    ]));

})->name('lineup.delete');