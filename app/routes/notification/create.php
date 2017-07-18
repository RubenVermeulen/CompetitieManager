<?php

$app->get('/notifications/create', $admin(), function() use ($app) {

    $app->render('notification/create.twig', [
        'rdTitle' => $app->config->get('notifications.title'),
        'rdTopic' => $app->config->get('notifications.topic'),
    ]);

})->name('notification.create');