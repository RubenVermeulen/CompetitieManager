<?php

use Carbon\Carbon;

$app->get('/seasons/:seasonId/games/:gameId/edit', $admin(), function($seasonId, $gameId) use($app) {

    try {
        $game = $app->game->findOrFail($gameId);
    }
    catch (Exception $e) {
        $app->notFound();
    }

    $app->render('game/edit.twig', [
        'game' => $game,
        'gameId' => $gameId,
        'seasonId' => $seasonId
    ]);

})->name('game.edit');

/*
 * Handle requests.
 */

$app->post('/seasons/:seasonId/games/:gameId/edit', $admin(), function($seasonId, $gameId) use($app) {

    $request = $app->request;

    $competitor = $request->post('competitor');
    $home = $request->post('home');

    if ($home === 'on') {
        $city = $zipCode = $streetName = $streetNumber = null;
    }
    else {
        $city = $request->post('city');
        $zipCode = $request->post('zip_code');
        $streetName = $request->post('street_name');
        $streetNumber = $request->post('street_number');
    }

    $date = $request->post('date');
    $time = $request->post('time');

    $v = $app->validation;

    /*
     * Validation rules which we are sure will run.
     */
    $validationRules = [
        'competitor|Tegenstander' => [$competitor, 'required|max(50)'],
        'date|Datum' => [$date, 'required|date'],
        'time|Tijd' => [$time, 'required']
    ];

    /*
     * Change validation rules if home is off.
     */
    if ($home === 'on') {
        $validationRulesAddress = [];
    }
    else {
        $validationRulesAddress = [
            'city|Stad' => [$city, 'required|max(50)'],
            'zip_code|Postcode' => [$zipCode, 'required|max(4)|min(4)|int'],
            'street_name|Straatnaam' => [$streetName, 'required|max(50)'],
            'street_number|Straatnummer' => [$streetNumber, 'required|alnum|max(50)'],
        ];
    }

    $v->validate(array_merge($validationRules, $validationRulesAddress));

    if ($v->passes()) {
        $app->game->where('id', $gameId)->update([
            'competitor' => $competitor,
            'home' => ($home === 'on') ? true : false,
            'city' => $city,
            'zip_code' => $zipCode,
            'street_name' => $streetName,
            'street_number' => $streetNumber,
            'played_at' => Carbon::createFromTimestamp(strtotime($date . $time))
        ]);

        $app->flash('global', 'De wedstrijd is gewijzigd.');
        $app->response->redirect($app->urlFor('game.show', [
            'seasonId' => $seasonId,
            'gameId' => $gameId
        ]));
    }

    $app->render('game/edit.twig', [
        'errors' => $v->errors(),
        'request' => $request,
        'gameId' => $gameId,
        'seasonId' => $seasonId
    ]);

})->name('game.edit.post');