<?php

$app->get('/users/create', $admin(), function() use($app) {

    $app->render('user/create.twig');

})->name('user.create');

$app->post('/users/create', $admin(), function() use($app) {

    $request = $app->request;

    $email = $request->post('email');
    $username = $request->post('username');
    $permission = $request->post('permission');

    $v = $app->validation;

    $v->validate([
        'email|E-mailadres' => [$email, 'required|email|uniqueEmail|max(255)'],
        'username|Gebruikersnaam' => [$username, 'required|alnumDash|max(20)|uniqueUsername|max(45)'],
        'permission|Rechten' => [$permission, 'required|int'],
    ]);

    if ($v->passes()) {
        $identifier = $app->randomlib->generateString(128);

        $user = $app->user->create([
            'email' => $email,
            'username' => $username,
            'active' => false,
            'active_hash' => $app->hash->hash($identifier)
        ]);

        $user->permissions()->create([
            'is_admin' => ($permission == 2 ? true : false),
        ]);

        /*
         * Send an email.
         */
        $app->mail->send('email/auth/registered.twig', ['user' => $user, 'identifier' => $identifier, 'username' => $username], [
            'to' => $user->email,
            'subject' => 'Je account is aangemaakt.',
        ]);

        $app->flash('global', 'De gebruiker is aangemaakt. Hij/zij zal binnenkort een e-mail ontvangen met verdere intructies.');
        $app->response->redirect($app->urlFor('user.all'));
    }

    /*
     * Validation failed.
     */
    $app->render('user/create.twig', [
        'errors' => $v->errors(),
        'request' => $request,
    ]);

})->name('user.create.post');