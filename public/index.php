<?php
define('start_time_D46', microtime(true));
date_default_timezone_set('America/Sao_Paulo');

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
header('Content-type: text/html; charset=UTF-8');

// set a constant that holds the project's folder path, like "/var/www/".
// DIRECTORY_SEPARATOR adds a slash to the end of the path
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// set a constant that holds the project's "application" folder, like "/var/www/application".
define('APP', ROOT . 'src' . DIRECTORY_SEPARATOR);

// set version plugins
define('PLUGINSVERSION', 'v_01');
// set version js
define('JSVERSION', 'v_01');

// set version css
define('CSSVERSION', 'v_01');

// This is the auto-loader for Composer-dependencies (to load tools into your project).
require ROOT . 'vendor/autoload.php';

// load application config (error reporting etc.)
require APP . 'config/config.php';

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
// "secure" only over HTTPS, otherwise the browser drops the cookie and login breaks on plain http
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
ini_set('session.cookie_secure', $isHttps ? 1 : 0);

// load application class
use RR\core\Application;

// start the application
$app = new Application();
