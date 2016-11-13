<?php

$app->get('/seasons/:seasonId/edit', $admin(), function($seasonId) use($app) {

    /*
     * Does the season exists.
     */
    try {
        $season = $app->season->findOrFail($seasonId);
    } catch (Exception $e) {
        $app->notFound();
    }

    $app->render('season/edit.twig', [
        'seasonId' => $seasonId,
        'season' => $season
    ]);

})->name('season.edit');

$app->post('/seasons/:seasonId/edit', $admin(), function($seasonId) use($app) {

    /*
     * Does the season exists.
     */
    try {
        $season = $app->season->findOrFail($seasonId);
    } catch (Exception $e) {
        $app->notFound();
    }

    $request = $app->request;

    $name = $request->post('name');

    $v = $app->validation;

    $v->validate([
        'name|Naam' => [$name, 'required|max(50)']
    ]);

    if ($v->passes()) {
        /*
         * Update record.
         */
        $season->update([
            'name' => $name
        ]);

        /*
         * Generate flash message and redirect.
         */
        $app->flash('global', 'Het seizoen is gewijzigd.');
        $app->redirect($app->urlFor('game.all', [
            'seasonId' => $seasonId
        ]));
    }

    $app->render('season/edit.twig', [
        'seasonId' => $seasonId,
        'errors' => $v->errors(),
        'request' => $request
    ]);

})->name('season.edit.post');