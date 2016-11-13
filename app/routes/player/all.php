<?php

$app->get('/players', $authenticated(), function() use($app) {
    $players = $app->player->orderBy('first_name')->orderBy('last_name')->get();

    $app->render('player/all.twig', [
        'players' => $players
    ]);

})->name('player.all');
