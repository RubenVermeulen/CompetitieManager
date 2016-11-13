<?php

$app->get('/seasons/:seasonId/games', $authenticated(), function($seasonId) use($app) {

    try {
        $season = $app->season->findOrFail($seasonId);
    } catch (Exception $e) {
        $app->notFound();
    }

    $games = $season->games()->oldest('played_at')->get();

    $app->render('game/all.twig', [
        'seasonId' => $seasonId,
        'seasonName' => $season->name,
        'games' => $games
    ]);

})->name('game.all');
