<?php

$app->delete('/players/:id', $admin(), function($id) use($app) {

    $player = $app->player->find($id);

    /*
     * If a player is connected to matches we will not delete the player.
     */
    if (count($player->matches) > 0) {
        $app->flash('global', 'De speler kon niet worden verwijderd, omdat hij al matchen gespeeld heeft.');
    }
    else {
        $player->delete();
        $app->flash('global', 'De speler is verwijderd.');
    }

    $app->response->redirect($app->urlFor('player.all'));

})->name('player.edit.delete');