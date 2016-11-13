<?php

use Carbon\Carbon;

$app->get('/players/create', $admin(), function() use($app) {

    $app->render('player/create.twig');

})->name('player.create');

/*
 * Handle requests.
 */

$app->post('/players/create', $authenticated(), function() use($app) {

    $request = $app->request;

    $firstName = $request->post('first_name');
    $lastName = $request->post('last_name');
    $email = $request->post('email');
    $telephone = $request->post('telephone');
    $membershipId = $request->post('membership_id');
    $profileUrl = $request->post('profile_url');

    $v = $app->validation;

    $v->validate([
        'first_name|Voornaam' => [$firstName, 'required|max(50)|alpha'],
        'last_name|Achternaam' => [$lastName, 'required|max(50)|alpha'],
        'email|E-mail' => [$email, 'required|max(255)|email'],
        'telephone|Telefoon' => [$telephone, 'required|int|max(50)'],
        'membership_id|Lidnummer' => [$membershipId, 'required|min(8)|max(8)|int|uniqueMembershipId'],
        'profile_url|Profiel URL' => [$profileUrl, 'url'],
    ]);

    if ($v->passes()) {

        /*
         * Create the new player.
         */
        $app->player->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'telephone' => $telephone,
            'membership_id' => $membershipId,
            'profile_url' => $profileUrl,
        ]);

        $app->flash('global', 'De speler is aangemaakt');
        $app->response->redirect($app->urlFor('player.all'));
    }

    $app->render('player/create.twig', [
        'errors' => $v->errors(),
        'request' => $request
    ]);

})->name('player.create.post');