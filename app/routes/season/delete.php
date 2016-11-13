<?php

$app->delete('/seasons/:seasonId', $admin(), function($seasonId) use($app) {

    /*
     * Does the season exists.
     */
    try {
        $season = $app->season->findOrFail($seasonId);
    } catch (Exception $e) {
        $app->notFound();
    }

    $season->delete();

    $app->flash('global', 'Het seizoen is verwijderd.');
    $app->redirect($app->urlFor('home'));

})->name('season.delete');