<?php
use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

use Noodlehaus\Config;
use RandomLib\Factory as RandomLib;

use Mailgun\Mailgun;

use Rubol\DB\DB;
use Rubol\Game\Set;
use Rubol\User\User;
use Rubol\Game\Game;
use Rubol\Game\Match;
use Rubol\Mail\Mailer;
use Rubol\Helpers\Hash;
use Rubol\Player\Player;
use Rubol\Season\Season;
use Rubol\Validation\Validator;

use Rubol\Middleware\BeforeMiddleware;
use Rubol\Middleware\CsrfMiddleware;
use Rubol\Middleware\SeasonMiddleware;

/*
|--------------------------------------------------------------------------
| Timezone
|--------------------------------------------------------------------------
|
| Set the default timezone.
|
*/

date_default_timezone_set('Europe/Brussels');

/*
|--------------------------------------------------------------------------
| Time language
|--------------------------------------------------------------------------
|
| Set the time language.
|
*/

setlocale(LC_TIME, 'nl_NL');

/*
|--------------------------------------------------------------------------
| Sessions
|--------------------------------------------------------------------------
|
| Start sessions.
|
*/

session_cache_limiter(false);
session_start();

/*
|--------------------------------------------------------------------------
| Error reporting
|--------------------------------------------------------------------------
|
| Show error reports or not.
|
*/

ini_set('display_errors', 'On');

/*
|--------------------------------------------------------------------------
| Root URL
|--------------------------------------------------------------------------
|
| Set up the root URL as a constant.
|
*/

define('INC_ROOT', dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| Dependencies
|--------------------------------------------------------------------------
|
| Autoload the dependencies from the vendor folder.
|
*/

require INC_ROOT . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Slim instance
|--------------------------------------------------------------------------
|
| Create a new slim instance and define the current application mode.
| Pull in Twig and define the views path.
|
*/

$app = new Slim([
    'mode' => file_get_contents(INC_ROOT . '/mode.php'),
    'view' => new Twig(),
    'templates.path' => INC_ROOT . '/app/views'
]);

/*
|--------------------------------------------------------------------------
| Middlewares
|--------------------------------------------------------------------------
|
| Load all middelwares.
|
*/

$app->add(new BeforeMiddleware());
$app->add(new CsrfMiddleware());
$app->add(new SeasonMiddleware());

/*
|--------------------------------------------------------------------------
| Load config
|--------------------------------------------------------------------------
|
| Load config into Slim.
|
*/

$app->configureMode($app->config('mode'), function() use ($app) {
    $app->config = Config::load(INC_ROOT . '/app/config/' . trim($app->mode) . '.php');
});

/*
|--------------------------------------------------------------------------
| Eloquent
|--------------------------------------------------------------------------
|
| Boot up Eloquent for further use.
| New models will be able to extend Eloquent.
|
*/

require 'database.php';

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
|
| Include all filters.
|
*/

require 'filters.php';

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
| Include all routes.
|
*/

require 'routes.php';

/*
|--------------------------------------------------------------------------
| Authenticates user
|--------------------------------------------------------------------------
|
| Will hold the User instance of the logged in user.
| Callable throughout the application.
|
| Assigned by the BeforeMiddleware.
|
*/

$app->auth = false;

/*
|--------------------------------------------------------------------------
| User instance
|--------------------------------------------------------------------------
|
| Create a new user instance.
| Callable throughout the application.
|
*/

$app->container->set('user', function() {
    return new User();
});

/*
|--------------------------------------------------------------------------
| Hash instance
|--------------------------------------------------------------------------
|
| Create a new hash instance. Only created once.
| Callable throughout the application.
|
*/

$app->container->singleton('hash', function() use ($app) {
    return new Hash($app->config);
});

/*
|--------------------------------------------------------------------------
| DB instance
|--------------------------------------------------------------------------
|
| Create a new db instance. Only created once.
| Callable throughout the application.
|
*/

$app->container->singleton('db', function() use($capsule) {
    return new DB($capsule);
});

/*
|--------------------------------------------------------------------------
| Mailer instance
|--------------------------------------------------------------------------
|
| Create a new mailer instance. Only created once.
| Callable throughout the application.
|
*/

$app->container->singleton('mail', function() use($app) {
    $mailer = new Mailgun($app->config->get('mail.api_key'));

    return new Mailer($app->config, $app->view, $mailer);
});

/*
|--------------------------------------------------------------------------
| RandomLib instance
|--------------------------------------------------------------------------
|
| Create a new RandomLib instance, and set the strength of the generator.
| Only created once.
| Callable throughout the application.
|
*/

$app->container->singleton('randomlib', function() {
    $factory = new RandomLib();

    return $factory->getMediumStrengthGenerator();
});

/*
|--------------------------------------------------------------------------
| Match instance
|--------------------------------------------------------------------------
|
| Create a new match instance.
| Callable throughout the application.
|
*/

$app->container->set('game', function() {
    return new Game();
});

/*
|--------------------------------------------------------------------------
| Player instance
|--------------------------------------------------------------------------
|
| Create a new player instance.
| Callable throughout the application.
|
*/

$app->container->set('player', function() {
    return new Player();
});

/*
|--------------------------------------------------------------------------
| Season instance
|--------------------------------------------------------------------------
|
| Create a new season instance.
| Callable throughout the application.
|
*/

$app->container->set('season', function() {
    return new Season();
});

/*
|--------------------------------------------------------------------------
| Match instance
|--------------------------------------------------------------------------
|
| Create a new match instance.
| Callable throughout the application.
|
*/

$app->container->set('match', function() {
    return new Match();
});

/*
|--------------------------------------------------------------------------
| Set instance
|--------------------------------------------------------------------------
|
| Create a new set instance.
| Callable throughout the application.
|
*/

$app->container->set('set', function() {
    return new Set();
});

/*
|--------------------------------------------------------------------------
| Validator instance
|--------------------------------------------------------------------------
|
| Create a new validator instance. Only created once.
| Callable throughout the application.
|
*/

$app->container->singleton('validation', function() use($app) {
    return new Validator($app->user, $app->hash, $app->auth, $app->player);
});

/*
|--------------------------------------------------------------------------
| Twig
|--------------------------------------------------------------------------
|
| Retrieve the view attribute which will be used to set up Twig.
|
*/

$view = $app->view();

/*
|--------------------------------------------------------------------------
| Twig debug mode
|--------------------------------------------------------------------------
|
| Set the debug mode for Twig.
|
*/

$view->parserOptions = [
    'debug' => $app->config->get('twig.debug')
];

/*
|--------------------------------------------------------------------------
| Twig Extension
|--------------------------------------------------------------------------
|
| Gives the ability to use helpers in the views.
|
*/

$view->parserExtensions = [
    new TwigExtension()
];

