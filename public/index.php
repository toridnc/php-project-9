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
use Carbon\Carbon;
use Slim\Flash\Messages;

session_start();

// Connect to PostgreSQL database
use PostgreSQLConnect\Connection as Connection;
try {
    $database = Connection::get()->connect();
    // echo 'A connection to the PostgreSQL database sever has been established successfully.';
} catch (\PDOException $e) {
    echo 'Connection error: ' . $e->getMessage();
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
$router = $app->getRouteCollector()->getRouteParser(); // Named routes

// HOMEPAGE and FORM FOR ADD URL
$app->get(
    '/',
    function ($request, $response) {
        $itemMenu = 'main';
        $messages = $this->get('flash')->getMessages();
        $params = [
            'itemMenu' => $itemMenu,
            'flash' => $messages,
            'url' => ''
        ];
        return $this->get('renderer')->render($response, 'index.phtml', $params);
    }
)->setName('homepage');

// Add NEW URL
$app->post(
    '/urls',
    function ($request, $response) use ($database, $router) {
        // Extract URL from the form
        $url = $request->getParsedBodyParam('url');
        $name = $url['name']; // 'name' for table 'urls' in database
        $timestamp = Carbon::now(); // 'created_at' for table 'urls' in database

        // Validation
        // If there are errors, set the response code to 422 and render the form with errors.
        $v = new Valitron\Validator($url);
        $v->rule('required', 'name'); // Field is not empty
        if (!$v->validate()) {
            $errors = $v->errors();
            $message = 'URL не должен быть пустым';
            $params = [
                'errors' => $errors,
                'message' => $message
            ];
            return $this->get('renderer')->render($response->withStatus(422), 'index.phtml', $params);
        }
        $v->rule('url', 'name'); // Correct URL address
        $v->rule('lengthMax', 'name', 255); // Length not more than 255 characters
        if (!$v->validate()) {
            $errors = $v->errors();
            $message = 'Некорректный URL';
            $params = [
                'errors' => $errors,
                'message' => $message
            ];
            return $this->get('renderer')->render($response->withStatus(422), 'index.phtml', $params);
        }

        // Check that the URL is already added
        $exists = $database->prepare('SELECT id FROM urls WHERE name=?');
        $exists->execute([$name]);
        $count = $exists->rowCount();
        // If the URL is already exists, set the response code to 422 and render the form with errors
        if ($count > 0) {
            $id = $exists->fetchColumn();
            $this->get('flash')->addMessage('warning', 'Страница уже существует');
            return $response->withRedirect($router->urlFor('url', ['id' => $id]), 302);
        }

        //  Add URL in database table 'urls'
        $addUrl = $database->prepare('INSERT INTO urls (name, created_at) VALUES (?, ?)');
        $addUrl->execute([$name, $timestamp]);
        $getId = $database->prepare('SELECT id FROM urls WHERE name=?');
        $getId->execute([$name]);
        $id = $getId->fetchColumn();
        // If the data is correct, save, add a flush and redirect
        $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
        return $response->withRedirect($router->urlFor('url', ['id' => $id]), 302);
    }
)->setName('postNewUrl');

// ALL URLS
$app->get(
    '/urls',
    function ($request, $response) use ($database) {
        $itemMenu = 'urls';
        $getUrls = $database->prepare("SELECT id, name FROM urls ORDER BY created_at DESC");
        $getUrls->execute();
        $allUrls = $getUrls->fetchAll();

        $urls = [];
        foreach ($allUrls as $url) {
            $id = $url['id'];
            $name = $url['name'];
            $urls[] = compact('id', 'name');
        }

        $params = [
            'itemMenu' => $itemMenu,
            'urls' => $urls
        ];
        return $this->get('renderer')->render($response, 'urls.phtml', $params);
    }
)->setName('urls');

// SHOW INFORM ABOUT ONE URL
$app->get(
    '/urls/{id}',
    function ($request, $response, $args) use ($database) {
        $id = $args['id'];
        // Add a message that the URL was added successfully.
        $messages = $this->get('flash')->getMessages();
        //$message = 'Страница успешно добавлена';

        $querySelect = $database->prepare('SELECT * FROM urls WHERE id=?');
        $querySelect->execute([$id]);
        $aboutUrl = $querySelect->fetch();

        $params = [
            'url' => $aboutUrl,
            'flash' => $messages
        ];
        return $this->get('renderer')->render($response, 'show.phtml', $params);
    }
)->setName('url');

$app->run();
