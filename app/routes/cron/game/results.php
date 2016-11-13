<?php

use \Rubol\Game\GetResults;

$app->get('/cron/game/results', function() use($app) {

    $request = $app->request;

    $url = $request->get('url');

    $v = $app->validation;

    $v->validate([
        'url|Url' => [$url, 'required|url'],
    ]);

    if ($v->passes()) {
        $results = new GetResults();
        $results->setUrl($url);

        echo $results->getResults();
    }
    else {
        echo 'failed';
    }

})->name('cron.game.results');