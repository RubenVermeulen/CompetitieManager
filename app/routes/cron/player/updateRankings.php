<?php

use \Rubol\Game\GetResults;

$app->get('/cron/:key/player/update-rankings', function($key) use($app) {

    /*
     * Check if key is valid.
     */
    $validKey = '688789a935de8c616f3977bd5a7c554cfd78fbc8371202227e929be336c6d166';

    if ($key !== $validKey) {
        $app->notFound();
    }

    $results = new GetResults();
    $players = $app->player->all();

    foreach ($players as $player) {
        if ($player->profile_url != null) {
            $url = $player->profile_url;

            $results->setUrl($url);
            $rankings = $results->getRankings();

            $player->update([
                'sm' => $rankings[0],
                'dm' => $rankings[1],
                'dx' => $rankings[2],
            ]);
        }
    }

})->name('cron.game.currentRanking');