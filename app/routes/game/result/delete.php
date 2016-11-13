<?php

$app->delete('/seasons/:seasonId/games/:gameId/result', $admin(), function($seasonId, $gameId) use($app) {

    $matches = $app->match->where('game_id', $gameId)->get();

    foreach ($matches as $match) {
        $app->set->where('match_id', $match->id)->delete();
    }

    $app->flash('global', 'De resultaten zijn verwijderd.');
    $app->response->redirect($app->urlFor('game.show', [
        'seasonId' => $seasonId,
        'gameId' => $gameId
    ]));

})->name('result.delete');