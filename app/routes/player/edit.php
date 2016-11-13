<?php

$app->get('/players/:id/edit', $admin(), function($id) use($app) {

    $player = $app->player->find($id);

    if ( ! $player) {
        $app->notFound();
    }

    $app->render('player/edit.twig', [
        'player' => $player,
        'id' => $id
    ]);

})->name('player.edit');

/*
 * Handle requests.
 */

$app->post('/players/:id/edit', $admin(), function($id) use($app) {

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
        'membership_id|Lidnummer' => [$membershipId, 'required|min(8)|max(8)|int'],
        'profile_url|Profiel URL' => [$profileUrl, 'url'],
    ]);

    if ($v->passes()) {
        $app->player->find($id)->update([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'telephone' => $telephone,
            'membership_id' => $membershipId,
            'profile_url' => $profileUrl,
        ]);

        $app->flash('global', 'De speler is gewijzigd.');
        $app->response->redirect($app->urlFor('player.show', ['id' => $id]));
    }

    $app->render('player/edit.twig', [
        'errors' => $v->errors(),
        'request' => $request,
        'id' => $id
    ]);

})->name('player.edit.post');