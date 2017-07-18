<?php
require INC_ROOT . '/app/routes/home.php';

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
|
| All routes concerning authentication.
|
*/

require INC_ROOT . '/app/routes/auth/login.php';
require INC_ROOT . '/app/routes/auth/logout.php';

require INC_ROOT . '/app/routes/auth/password/change.php';
require INC_ROOT . '/app/routes/auth/password/recover.php';
require INC_ROOT . '/app/routes/auth/password/reset.php';

/*
|--------------------------------------------------------------------------
| Account
|--------------------------------------------------------------------------
|
| All routes concerning the user account.
|
*/

require INC_ROOT . '/app/routes/account/profile.php';

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
|
| All routes concerning users, profiles, etc.
|
*/

require INC_ROOT . '/app/routes/user/all.php';
require INC_ROOT . '/app/routes/user/create.php';
require INC_ROOT . '/app/routes/user/activate.php';
require INC_ROOT . '/app/routes/user/edit.php';
require INC_ROOT . '/app/routes/user/delete.php';

/*
|--------------------------------------------------------------------------
| Errors
|--------------------------------------------------------------------------
|
| Route concerning custom 404.
|
*/

require INC_ROOT . '/app/routes/errors/404.php';

/*
|--------------------------------------------------------------------------
| Game
|--------------------------------------------------------------------------
|
| All routes concerning games.
|
*/

require INC_ROOT . '/app/routes/game/create.php';
require INC_ROOT . '/app/routes/game/all.php';
require INC_ROOT . '/app/routes/game/show.php';
require INC_ROOT . '/app/routes/game/edit.php';
require INC_ROOT . '/app/routes/game/delete.php';

require INC_ROOT . '/app/routes/game/lineup/create.php';
require INC_ROOT . '/app/routes/game/lineup/edit.php';
require INC_ROOT . '/app/routes/game/lineup/delete.php';

require INC_ROOT . '/app/routes/game/result/create.php';
require INC_ROOT . '/app/routes/game/result/edit.php';
require INC_ROOT . '/app/routes/game/result/delete.php';

/*
|--------------------------------------------------------------------------
| Player
|--------------------------------------------------------------------------
|
| All routes concerning players.
|
*/

require INC_ROOT . '/app/routes/player/create.php';
require INC_ROOT . '/app/routes/player/all.php';
require INC_ROOT . '/app/routes/player/stats.php';
require INC_ROOT . '/app/routes/player/show.php';
require INC_ROOT . '/app/routes/player/edit.php';
require INC_ROOT . '/app/routes/player/delete.php';

/*
|--------------------------------------------------------------------------
| Season
|--------------------------------------------------------------------------
|
| All routes concerning seasons.
|
*/

require INC_ROOT . '/app/routes/season/create.php';
require INC_ROOT . '/app/routes/season/edit.php';
require INC_ROOT . '/app/routes/season/delete.php';

/*
|--------------------------------------------------------------------------
| Notification
|--------------------------------------------------------------------------
|
| All routes concerning notifications.
|
*/

require INC_ROOT . '/app/routes/notification/create.php';

/*
|--------------------------------------------------------------------------
| Cron
|--------------------------------------------------------------------------
|
| All crons.
|
*/

require INC_ROOT . '/app/routes/cron/notification.php';
require INC_ROOT . '/app/routes/cron/game/results.php';
require INC_ROOT . '/app/routes/cron/player/updateRankings.php';