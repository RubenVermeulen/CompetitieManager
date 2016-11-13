<?php

$app->get('/activate', $guest(), function() use($app) {

    $request = $app->request;

    $email = $request->get('email');
    $identifier = $request->get('identifier');

    $hashedIdentifier = $app->hash->hash($identifier);

    $user = $app->user
        ->where('email', $email)
        ->where('active', false)
        ->first();

    /*
     * If we could not find a user or the hash is not correct,
     * we will redirect the user.
     */
    if ( ! $user || ! $app->hash->hashCheck($user->active_hash, $hashedIdentifier)) {
        $app->flash('error', 'We konden het account niet activeren.');
        $app->response->redirect($app->urlFor('login'));
    }

    /*
     * Render the view if all data is correct.
     */
    $app->render('user/activate.twig', [
        'username' => $user->username,
        'email' => $email,
        'identifier' => $identifier
    ]);

})->name('user.activate');

$app->post('/activate', $guest(), function() use($app) {

    $request = $app->request;

    $email = $request->get('email');
    $identifier = $request->get('identifier');

    $password = $request->post('password');
    $passwordConfirm = $request->post('password_confirm');

    $hashedIdentifier = $app->hash->hash($identifier);

    $user = $app->user
        ->where('email', $email)
        ->where('active', false)
        ->first();

    if ( ! $user) {
        $app->response->redirect($app->urlFor('home'));
    }

    if ( ! $app->hash->hashCheck($user->active_hash, $hashedIdentifier)) {
        $app->response->redirect($app->urlFor('home'));
    }

    $v = $app->validation;

    $v->validate([
        'password|Wachtwoord' => [$password, 'required|min(6)'],
        'password_confirm|Bevestig wachtwoord' => [$passwordConfirm, 'required|matches(password)']
    ]);

    if ($v->passes()) {
        $user->activateAccount($app->hash->password($password));

        $app->flash('global', 'Je account is succesvol geactiveerd. Gebruik je e-mailadres of gebruikersnaam om in te loggen.');
        $app->response->redirect($app->urlFor('login'));
    }

    $app->render('user/activate.twig', [
        'username' => $user->username,
        'email' => $email,
        'identifier' => $identifier,
        'errors' => $v->errors()
    ]);

})->name('user.activate.post');
