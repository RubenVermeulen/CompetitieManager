<?php

/*
|--------------------------------------------------------------------------
| Middlewares
|--------------------------------------------------------------------------
|
| All used for routing purposes.
|
*/

/**
 * Checks if user needs to be redirected.
 *
 * @param $required
 * @return callable
 */
$authenticationCheck = function($required) use($app) {
    return function() use($required, $app) {
        if (( ! $app->auth && $required) || ($app->auth && !$required)) {
            $app->redirect($app->urlFor('home'));
        }
    };
};

/**
 * Only authenticated users are allowed.
 *
 * @return mixed
 */
$authenticated = function() use ($authenticationCheck) {
    return $authenticationCheck(true);
};

/**
 * Only guest users are allowed.
 *
 * @return mixed
 */
$guest = function() use ($authenticationCheck) {
    return $authenticationCheck(false);
};

/**
 * Only admin users are allowed.
 *
 * @return callable
 */
$admin = function() use($app) {
    return function() use($app) {
        if ( ! $app->auth || ! $app->auth->isAdmin()) {
            $app->redirect($app->urlFor('home'));
        }
    };
};
