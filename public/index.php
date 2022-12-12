<?php

/**
 * Page analyzer
 *
 * PHP version 7.4
 *
 * @category xxx
 * @package  xxx
 * @author   toridnc <riadev@inbox.ru>
 * @license  MIT
 * @link     https://github.com/toridnc/php-project-lvl3
 */

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response) {
    return $response->write('Page analyzer');
});

$app->run();