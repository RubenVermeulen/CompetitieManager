<?php

$app->get('/users', $admin(),function() use($app) {

    $users = $app->user->where('id', '!=', $app->auth->id)->get();

    $app->render('user/all.twig', [
        'users' => $users
    ]);

})->name('user.all');