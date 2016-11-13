<?php

$app->get('/recover-password', $guest(), function() use($app) {

    $app->render('auth/password/recover.twig');

})->name('auth.password.recover');

$app->post('/recover-password', $guest(), function() use($app) {

    $request = $app->request;

    $email = $request->post('email');

    $v = $app->validation;

    $v->validate([
        'email|E-mailadres' => [$email, 'required|email']
    ]);

    if ($v->passes()) {
        $user = $app->user->where('email', $email)->first();

        if ( ! $user) {
            $app->flash('error', 'We konden geen gebruikers vinden.');
            $app->response->redirect($app->urlFor('auth.password.recover'));
        }
        else {
            $identifier = $app->randomlib->generateString(128);

            $user->update([
                'recover_hash' => $app->hash->hash($identifier)
            ]);

            /*
             * Send an email.
             */
            $app->mail->send('email/auth/password/recover.twig', ['user' => $user, 'identifier' => $identifier], [
                'to' => $user->email,
                'subject' => 'Reset je wachtwoord.',
            ]);

            $app->flash('global', 'We hebben je instructies gestuurd om je wachtwoord te reseten.');
            $app->response->redirect($app->urlFor('login'));
        }
    }

    $app->render('auth/password/recover.twig', [
        'errors' => $v->errors(),
        'request' => $request
    ]);

})->name('auth.password.recover.post');