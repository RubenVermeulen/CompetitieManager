<?php

$app->get('/password-reset', $guest(), function() use($app) {

    $request = $app->request;

    $email = $request->get('email');
    $identifier = $request->get('identifier');

    $hashedIdentifier = $app->hash->hash($identifier);

    $user = $app->user->where('email', $email)->first();

    if ( ! $user) {
        $app->response->redirect($app->urlFor('home'));
    }

    if ( ! $user->recover_hash) {
        $app->response->redirect($app->urlFor('home'));
    }

    if ( ! $app->hash->hashCheck($user->recover_hash, $hashedIdentifier)) {
        $app->response->redirect($app->urlFor('home'));
    }

    $app->render('auth/password/reset.twig', [
        'email' => $email,
        'identifier' => $identifier
    ]);

})->name('auth.password.reset');

$app->post('/password-reset', $guest(), function() use($app) {

    $request = $app->request;

    $email = $request->get('email');
    $identifier = $request->get('identifier');

    $password = $request->post('password');
    $passwordConfirm = $request->post('password_confirm');

    $hashedIdentifier = $app->hash->hash($identifier);

    $user = $app->user->where('email', $email)->first();

    if ( ! $user) {
        $app->response->redirect($app->urlFor('home'));
    }

    if ( ! $user->recover_hash) {
        $app->response->redirect($app->urlFor('home'));
    }

    if ( ! $app->hash->hashCheck($user->recover_hash, $hashedIdentifier)) {
        $app->response->redirect($app->urlFor('home'));
    }

    $v = $app->validation;

    $v->validate([
        'password|Wachtwoord' => [$password, 'required|min(6)'],
        'password_confirm|Bevestig wachtwoord' => [$passwordConfirm, 'required|matches(password)']
    ]);

    if ($v->passes()) {
        $user->update([
            'password' => $app->hash->password($password),
            'recover_hash' => null
        ]);

        $app->flash('global', 'Je wachtwoord is gereset, je kan nu inloggen.');
        $app->redirect($app->urlFor('login'));
    }

    $app->render('auth/password/reset.twig', [
        'email' => $email,
        'identifier' => $identifier,
        'errors' => $v->errors()
    ]);

})->name('auth.password.reset.post');