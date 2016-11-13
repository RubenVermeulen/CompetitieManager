<?php

$app->get('/seasons/:seasonId/games/:gameId/lineup/create', $admin(), function($seasonId, $gameId) use($app) {

    try {
        $app->season->findOrFail($seasonId);
        $app->game->findOrFail($gameId);
    }
    catch (Exception $e) {
        $app->notFound();
    }

    $players = $app->player->orderBy('first_name')->orderBy('last_name')->get();

    $app->render('game/lineup/create.twig', [
        'seasonId' => $seasonId,
        'gameId' => $gameId,
        'players' => $players
    ]);

})->name('lineup.create');

$app->post('/seasons/:seasonId/games/:gameId/lineup/create', $admin(), function($seasonId, $gameId) use($app) {

    $request = $app->request;

    /*
     * Create array for validation.
     */
    $validationRules = [];

    /*
     * Count variable is used to determine player alias for error message.
     */
    $count = 1;

    /*
     * Create variables doubles and add validation rules.
     */
    for ($i = 1; $i <= 4; $i++) {
        ${'d' . $i  . '1'} = $request->post('d' . $i  . '1');
        ${'d' . $i  . '2'} = $request->post('d' . $i  . '2');

        $validationRules['d' . $i  . '1|Speler ' . $count++] = [${'d' . $i  . '1'}, 'required'];
        $validationRules['d' . $i  . '2|Speler ' . $count] = [${'d' . $i  . '2'}, 'required'];

        $count = 1;
    }

    /*
     * Create variables singles and add validation rules.
     */
    for ($i = 1; $i <= 4; $i++) {
        ${'s' . $i} = $request->post('s' . $i);

        $validationRules['s' . $i . '|Enkel ' . $i] = [${'s' . $i}, 'required'];
    }

    $v = $app->validation;


    $v->validate($validationRules);

    if ($v->passes()) {
        $game = $app->game->find($gameId);

        /*
         * Insert all doubles.
         */
        for($i = 1; $i <= 4; $i++) {
            /*
             * Create a match record.
             */
            $match = $game->matches()->create([
               'type' => 2
            ]);

            /*
             * Create a record in intermediate table.
             */
            $match->players()->attach(${'d' . $i  . '1'});
            $match->players()->attach(${'d' . $i  . '2'});
        }

        /*
         * Insert all singles.
         */
        for($i = 1; $i <= 4; $i++) {
            /*
             * Create a match record.
             */
            $match = $game->matches()->create([
                'type' => 1
            ]);

            /*
             * Create a record in intermediate table.
             */
            $match->players()->attach(${'s' . $i});
        }

        /*
         * Redirect after success.
         */
        $app->flash('global', 'De lineup is aangemaakt.');
        $app->redirect($app->urlFor('game.show', [
            'seasonId' => $seasonId,
            'gameId' => $gameId
        ]));
    }

    /*
     * Validation fails.
     */
    $players = $app->player->orderBy('first_name')->orderBy('last_name')->get();

    $app->render('game/lineup/create.twig', [
        'seasonId' => $seasonId,
        'gameId' => $gameId,
        'players' => $players,
        'errors' => $v->errors(),
        'request' => $request
    ]);

})->name('lineup.create.post');
