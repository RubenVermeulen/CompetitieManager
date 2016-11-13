<?php

$app->delete('/users/:userId', $admin(), function($userId) use($app) {

    /*
     * Does the user exists.
     */
    try {
        $user = $app->user->findOrFail($userId);
    } catch (Exception $e) {
        $app->notFound();
    }

    $user->delete();

    $app->flash('global', 'De gebruiker is verwijderd.');
    $app->redirect($app->urlFor('user.all'));

})->name('user.delete');