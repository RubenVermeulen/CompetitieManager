<?php

use Carbon\Carbon;

$app->get('/seasons/:seasonId/games/:gameId/result/create', $admin(),function($seasonId, $gameId) use($app) {

    try {
        $app->season->findOrFail($seasonId);
        $game = $app->game->findOrFail($gameId);

        /*
         * Are there already matches to give results.
         */
        if (count($game->matches) === 0) {
            throw new Exception();
        }
    }
    catch (Exception $e) {
        $app->notFound();
    }

    $app->render('game/result/create.twig', [
        'seasonId' => $seasonId,
        'gameId' => $gameId
    ]);

})->name('result.create');

$app->post('/seasons/:seasonId/games/:gameId/result/create', $admin(),function($seasonId, $gameId) use($app) {

    $request = $app->request;

    $validationRules = [];

    /*
     * Add all doubles to the validation rules.
     */
    for($i = 1; $i <= 4; $i++) { // number of double
        for($j = 1; $j <= 3; $j++) { // number of set
            $field = "d{$i}s{$j}";

            /*
             * The third field is optional so the required is not necessary.
             */
            if ($j === 3) {
                $rules = 'resultFormat';
            }
            else {
                $rules = 'required|resultFormat';
            }

            $validationRules[$field . '|Set ' . $j] = [$request->post($field), $rules];
        }
    }

    /*
     * Add all singles to the validation rules
     */

    for($i = 1; $i <= 4; $i++) { // number of double
        for($j = 1; $j <= 3; $j++) { // number of set
            $field = "s{$i}s{$j}";

            /*
             * The third field is optional so the required is not necessary.
             */
            if ($j === 3) {
                $rules = 'resultFormat';
            }
            else {
                $rules = 'required|resultFormat';
            }

            $validationRules[$field . '|Set ' . $j] = [$request->post($field), $rules];
        }
    }

    $v = $app->validation;

    $v->validate($validationRules);

    if($v->passes()) {
        /*
         * Fetch the game to get access to al the matches.
         */
        $game = $app->game->find($gameId);

        /*
         * Fetch the matches.
         */
        $matches = $game->matches;

        /*
         * Loop through all matches.
         */
        $countDouble = 1;
        $countSingle = 1;
        $insertArray = [];

        foreach ($matches as $match) {
            /*
             * Create 3 sets.
             */
            for ($j = 1; $j <= 3; $j++) {
                /*
                 * Build the field reference.
                 */
                if ( $match->type === 1) {
                    $field = "s{$countSingle}s{$j}";
                }
                else {
                    echo '2<br>';
                    $field = "d{$countDouble}s{$j}";
                }

                /*
                 * If the third field has no value break out of the loop.
                 * This way we avoid a NULL record in the database.
                 */
                if (empty($request->post($field))) {
                    $results = [null, null];
                }
                else {
                    /*
                     * Split the field to retrieve the two numbers.
                     */
                    $results = explode(' ', $request->post($field));
                }

                /*
                 * Add set to array.
                 */
                $insertArray[] = [
                    'match_id' => $match->id,
                    'result_one' => $results[0],
                    'result_two' => $results[1],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            /*
             * Increment double or single.
             * This is used for the correct field name.
             */
            ($match->type === 1) ? $countSingle++ : $countDouble++;
        }

        /*
         * Insert all records as a batch.
         */
        $app->set->insert($insertArray);

        /*
         * Create a message and redirect after success.
         */
        $app->flash('global', 'Het resultaat is aangemaakt.');
        $app->redirect($app->urlFor('game.show', [
            'seasonId' => $seasonId,
            'gameId' => $gameId
        ]));
    }

    $app->render('game/result/create.twig', [
        'seasonId' => $seasonId,
        'gameId' => $gameId,
        'errors' => $v->errors(),
        'request' => $request
    ]);

})->name('result.create.post');