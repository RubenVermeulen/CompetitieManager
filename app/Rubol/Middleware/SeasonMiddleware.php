<?php

namespace Rubol\Middleware;

use Slim\Middleware;

class SeasonMiddleware extends Middleware
{

    /**
     * Call
     *
     * Perform actions specific to this middleware and optionally
     * call the next downstream middleware.
     */
    public function call() {
        $this->app->hook('slim.before', [$this, 'seasons']);
        $this->next->call();
    }

    /**
     * Make all seasons accessible for all views.
     */
    public function seasons() {
        $seasons = $this->app->season->latest('created_at')->get();

        $this->app->view()->appendData([
            'seasons' => $seasons
        ]);
    }
}