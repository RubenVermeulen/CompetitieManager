<?php

$app->get('/notifications/create', $admin(), function() use ($app) {

    $app->render('notification/create.twig', [
        'rdTitle' => $app->config->get('notifications'),
        'rdTopic' => $app->config->get('notifications'),
    ]);

})->name('notification.create');