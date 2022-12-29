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

// Connect to postgreSQL database
use PostgreSQLConnect\Connection as Connection;
try {
    Connection::get()->connect();
    echo 'A connection to the PostgreSQL database sever has been established successfully.';
} catch (\PDOException $e) {
    echo $e->getMessage();
}

// Connect to PHPRenderer templates
$container = new Container();
$container->set(
    'renderer',
    function () {
        return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
    }
);

// Connect to flash messages
$container->set(
    'flash',
    function () {
        return new \Slim\Flash\Messages();
    }
);

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);

// HOMEPAGE and FORM FOR ADD URL
$app->get(
    '/',
    function ($request, $response) {
        $itemMenu = 'main';
        $params = [
            'itemMenu' => $itemMenu,
            'url' => $url,
            'errors' => $errors
        ];
        return $this->get('renderer')->render($response, 'index.phtml', $params);
    }
)->setName('homepage');

$app->get(
    '/phpinfo',
    function ($request, $response) {
        $itemMenu = 'main';
        $params = [
            'itemMenu' => $itemMenu,
            'url' => $url,
            'errors' => $errors
        ];
        return $this->get('renderer')->render($response, 'phpinfo.phtml', $params);
    }
)->setName('phpinfo');

// Add NEW URL
$app->post(
    '/urls',
    function ($request, $response) {
        $url = $request->getParsedBodyParam('url');
        if (count($errors) === 0) {
            // If the data is correct, save, add a flush and redirect.
            $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
            return $response->withRedirect($router->urlFor('url'), 302);
        }
        $params = [
            'url' => $url,
            'errors' => $errors
        ];
        // If there are errors, we set the response code to 422 and render the form with errors.
        return $this->get('renderer')->render($response->withStatus(422), 'users/index.phtml', $params);
    }
)->setName('addUrl');

// ALL URLS
$app->get(
    '/urls',
    function ($request, $response) {
        $itemMenu = 'urls';
        $params = [
            'itemMenu' => $itemMenu,
            'errors' => $errors
        ];
        return $this->get('renderer')->render($response, 'urls.phtml', $params);
    }
)->setName('urls');

// SHOW INFORM ABOUT ONE URL
$app->get(
    '/urls/{id}',
    function ($request, $response, $args) {
        return $this->get('renderer')->render($response, 'show.phtml');
    }
)->setName('url');

$app->run();
