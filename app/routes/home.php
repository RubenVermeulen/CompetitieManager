<?php

$app->get('/', function() use ($app) {

    $app->flashKeep();

    if ( ! $app->auth) {
        $app->redirect($app->urlFor('login'));
    }
    else {
        $lastestSeason = $app->season->latest('created_at')->first();

        if (! $lastestSeason) {
            $app->flash('info', 'Je hebt nog geen seizoen, maak er eerst één aan.');
            $app->redirect($app->urlFor('season.create'));
        }

        $app->redirect($app->urlFor('game.all', [
            'seasonId' => $lastestSeason->id
        ]));
    }

})->name('home');