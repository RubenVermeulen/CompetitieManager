<?php

namespace Rubol\Middleware;

use Slim\Middleware;
use Carbon\Carbon;

class BeforeMiddleware extends Middleware
{
    public function call() {
        $this->app->hook('slim.before', [$this, 'run']);

        $this->next->call();
    }

    /**
     * Check if session exists. If true then assign a user instance to auth.
     * Also we will pass the auth value to all views. It either contains a user instance,
     * or false.
     */
    public function run() {
        if(isset($_SESSION[$this->app->config->get('auth.session')])) {
            $this->app->auth = $this->app->user->where('id', $_SESSION[$this->app->config->get('auth.session')])->first();
        }

        $this->checkRememberMe();

        $this->app->view()->appendData([
            'auth' => $this->app->auth,
            'baseUrl' => $this->app->config->get('app.url'),
            'subUrl' => $this->app->config->get('app.subUrl'),
            'apiUrl' => $this->app->config->get('app.apiUrl'),
        ]);
    }

    /**
     * Check if a cookie is set. If all credentials are correct, a session will be created.
     * If not, then all credentials in the database will be removed for security reasons.
     */
    protected function checkRememberMe() {
        if ($this->app->getCookie($this->app->config->get('auth.remember')) && ! $this->app->auth) {
            $data = $this->app->getCookie($this->app->config->get('auth.remember'));
            $credentials = explode('___', $data);

            if (empty(trim($data)) || count($credentials) !== 2) {
                $this->app->response->redirect($this->app->urlFor('home'));
            }
            else {
                $identifier = $credentials[0];
                $token = $this->app->hash->hash($credentials[1]);

                $user = $this->app->user
                    ->where('remember_identifier', $identifier)
                    ->first();

                if ($user) {
                    if ($this->app->hash->hashCheck($token, $user->remember_token)) {
                        /*
                         * Set session with user id.
                         */
                        $_SESSION[$this->app->config->get('auth.session')] = $user->id;

                        /*
                         * Instantiate auth variable with the corresponding User object.
                         */
                        $this->app->auth = $this->app->user->where('id', $user->id);

                        /*
                         * Generate a new remember token.
                         */
                        $rememberToken = $this->app->randomlib->generateString(128);

                        $user->updateRememberCredentials(
                            $identifier,
                            $this->app->hash->hash($rememberToken)
                        );

                        $this->app->setCookie(
                            $this->app->config->get('auth.remember'),
                            "{$identifier}___{$rememberToken}",
                            Carbon::parse('+6 month')->timestamp
                        );
                    }
                    else {
                        $user->removeRememberCredentials();
                    }
                }
            }
        }
    }
}