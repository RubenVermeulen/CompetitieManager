<?php

$app->get('/seasons/:seasonId/games/:gameId/result/edit', $admin(),function($seasonId, $gameId) use($app) {

    try {
        $app->season->findOrFail($seasonId);
        $game = $app->game->with('matches.sets')->findOrFail($gameId);
    }
    catch (Exception $e) {
        $app->notFound();
    }

    $app->render('game/result/edit.twig', [
        'seasonId' => $seasonId,
        'gameId' => $gameId,
        'matches' => $game->matches
    ]);

})->name('result.edit');

$app->post('/seasons/:seasonId/games/:gameId/result/edit', $admin(),function($seasonId, $gameId) use($app) {

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

        foreach ($matches as $match) {
            /*
             * Retrieve all sets.
             */
            $sets = $match->sets;

            /*
             * Update sets.
             */
            for ($j = 1; $j <= 3; $j++) {
                /*
                 * Build the field reference.
                 */
                if ($match->type === 1) {
                    $field = "s{$countSingle}s{$j}";
                }
                else {
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
                 * Update set.
                 */
//                die(print_r($sets[$j - 1]));
                $sets[$j - 1]->update([
                    'result_one' => $results[0],
                    'result_two' => $results[1]
                ]);
            }

            /*
             * Increment double or single.
             * This is used for the correct field name.
             */
            ($match->type === 1) ? $countSingle++ : $countDouble++;
        }

        /*
         * Create a message and redirect after success.
         */
        $app->flash('global', 'Het resultaat is gewijzigd.');
        $app->redirect($app->urlFor('game.show', [
            'seasonId' => $seasonId,
            'gameId' => $gameId
        ]));
    }

    $app->render('game/result/edit.twig', [
        'seasonId' => $seasonId,
        'gameId' => $gameId,
        'errors' => $v->errors(),
        'request' => $request
    ]);

})->name('result.edit.post');