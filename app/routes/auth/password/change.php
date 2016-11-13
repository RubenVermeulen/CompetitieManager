<?php

$app->get('/change-password', $authenticated(), function() use($app) {
    $app->render('auth/password/change.twig');
})->name('auth.password.change');

$app->post('/change-password', $authenticated(), function() use($app) {

    $request = $app->request;

    $passwordOld = $request->post('password_old');
    $password = $request->post('password');
    $passwordConfirm = $request->post('password_confirm');

    $v = $app->validation;

    $v->validate([
        'password_old|Oud wachtwoord' => [$passwordOld, 'required|matchesCurrentPassword'],
        'password|Wachtwoord' => [$password, 'required|min(6)'],
        'password_confirm|Bevestig wachtwoord' => [$passwordConfirm, 'required|matches(password)']
    ]);

    if ($v->passes()) {
        $user = $app->auth;

        $user->update([
            'password' =>$app->hash->password($password)
        ]);

        /*
         * Send an email.
         */
        $app->mail->send('email/auth/password/changed.twig', [], [
            'to' => $user->email,
            'subject' => 'Je wachtwoord is gewijzigd.',
        ]);

        $app->flash('global', 'Je wachtwoord is gewijzigd.');
        $app->response->redirect($app->urlFor('home'));
    }

    $app->render('auth/password/change.twig', [
        'errors' => $v->errors()
    ]);

})->name('auth.password.change.post');