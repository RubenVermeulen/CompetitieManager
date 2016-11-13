<?php

$app->get('/users/:userId/edit', $admin(), function($userId) use($app) {

    $user = $app->user->with('permissions')->find($userId);

    if ( ! $user) {
        $app->notFound();
    }

    $app->render('user/edit.twig', [
        'userId' => $userId,
        'user' => $user,
    ]);

})->name('user.edit');

$app->post('/users/:userId/edit', $admin(), function($userId) use($app) {

    try {
        $user = $app->user->findOrFail($userId);
    } catch (Exception $e) {
        $app->notFound();
    }

    $request = $app->request;

    $email = $request->post('email');
    $permission = $request->post('permission');

    $v = $app->validation;

    $v->validate([
        'email|E-mailadres' => [$email, "required|email|uniqueEmail({$user->email})|max(255)"],
        'permission|Rechten' => [$permission, 'required|int'],
    ]);

    if ($v->passes()) {
        $user->update([
            'email' => $email,
        ]);

        $user->permissions()->update([
            'is_admin' => ($permission == 2 ? true : false),
        ]);

        $app->flash('global', 'De gebruiker is gewijzigd.');
        $app->redirect($app->urlFor('user.all'));
    }

    /*
     * Validation failed.
     */
    $app->render('user/edit.twig', [
        'userId' => $userId,
        'errors' => $v->errors(),
        'request' => $request,
    ]);

})->name('user.edit.post');