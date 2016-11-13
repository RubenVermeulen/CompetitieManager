<?php

$app->get('/seasons/create', $admin(), function() use($app) {

    $app->render('season/create.twig');

})->name('season.create');

$app->post('/seasons/create', $admin(), function() use($app) {

    $request = $app->request;

    $name = $request->post('name');

    $v = $app->validation;

    $v->validate([
        'name|Naam' => [$name, 'required|max(50)']
    ]);

    if ($v->passes()) {

        $app->season->create([
            'name' => $name
        ]);

        $app->flash('global', 'Het seizoen is aangemaakt.');
        $app->redirect($app->urlFor('home'));
    }

    $app->render('season/create.twig', [
        'errors' => $v->errors(),
        'request' => $request
    ]);

})->name('season.create.post');