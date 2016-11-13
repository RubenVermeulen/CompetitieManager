<?php

$app->delete('/seasons/:seasonId/games/:gameId/edit', $admin(), function($seasonId, $gameId) use($app) {

    $app->game->find($gameId)->delete();

    $app->flash('global', 'De wedstrijd is verwijderd.');
    $app->response->redirect($app->urlFor('game.all', [
        'seasonId' => $seasonId
    ]));

})->name('game.delete');