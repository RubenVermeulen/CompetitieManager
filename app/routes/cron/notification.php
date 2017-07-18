<?php

use Carbon\Carbon;

/*
 * Send an email to all listed persons informing the upcoming matches.
 */
$app->get('/cron/:key/notification', function($key) use($app) {

    /*
     * Check if key is valid.
     */
    $validKey = '688789a935de8c616f3977bd5a7c554cfd78fbc8371202227e929be336c6d166';

    if ($key !== $validKey) {
        $app->notFound();
    }

    $users = $app->user->all();
    $to = '';
    $x = 1;

    foreach ($users as $user) {
        $to .= $user->email;

        /*
         * Add a comma when there are still users left in the loop.
         */
        if ($x < count($users)) {
            $to .= ', ';
        }

        $x++;
    }

    /*
     * Get all games which will occur in 7 days from now.
     */
    $games = $app->game
        ->with('season')
        ->where('played_at', '>=', Carbon::now()->createFromTime(0,0,0)->addDay(7))
        ->where('played_at', '<=', Carbon::now()->createFromTime(23,59,59)->addDay(7))
        ->get();

    /*
     * Only sent an email when there are upcoming games.
     */
    if (count($games) !== 0) {
        $game = $games[0];

        /*
         * Send an email.
         */
        $app->mail->send('email/cron/notification.twig', ['games' => $games], [
            'to' => $to,
            'subject' => 'Herinnering komende wedstrijden.',
        ]);

        /*
         * Send a notification.
         */
        $client = new \GuzzleHttp\Client();
        $url = $app->config->get('app.apiUrl') . 'notifications';
        $location = $game->home ? 'thuis' : 'op verplaatsing';
        $payload = [
            'title' => 'Flee Shuttle 2H',
            'topic' => 'flee_shuttle_2h',
            'body' => "Volgende week {$game->getDayPlayedAt()} ({$game->getStandardDatePlayedAt()}) om {$game->getTimePlayedAt()} spelen we {$location} tegen {$game->competitor}."
        ];
        $client->post($url, ['json' => $payload]);
    }

});