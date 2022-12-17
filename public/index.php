<?php

/**
 * Page analyzer
 *
 * PHP version 7.4
 *
 * @category Project
 * @package  Page_Analyzer
 * @author   toridnc <riadev@inbox.ru>
 * @license  MIT https://mit-license.org/
 * @link     https://github.com/toridnc/php-project-lvl3
 */

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;
use DI\Container;

session_start();

$container = new Container();
$container->set(
    'renderer', function () {
        return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
    }
);
$container->set(
    'flash', function () {
        return new \Slim\Flash\Messages();
    }
);

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);

// HOMEPAGE
$app->get(
    '/', function ($request, $response) {
        return $this->get('renderer')->render($response, 'index.phtml');
    }
)->setName('homepage');

// ALL URLS
$app->get(
    '/urls', function ($request, $response) {
        return $this->get('renderer')->render($response, 'urls.phtml');
    }
)->setName('urls');

// SHOW INFORM ABOUT ONE URL
$app->get(
    '/urls/{id}', function ($request, $response) {
        return $this->get('renderer')->render($response, 'show.phtml');
    }
)->setName('url');

$app->run();
