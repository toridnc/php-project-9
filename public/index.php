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

session_start();

// Connect to PostgreSQL database
use PostgreSQLConnect\Connection as Connection;
try {
    $database = Connection::get()->connect();
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

$router = $app->getRouteCollector()->getRouteParser();

// HOMEPAGE and FORM FOR ADD URL
$app->get(
    '/',
    function ($request, $response) {
        $itemMenu = 'main';

        $params = [
            'itemMenu' => $itemMenu,
            'name' => ''
        ];
        return $this->get('renderer')->render($response, 'index.phtml', $params);
    }
)->setName('homepage');

// Add NEW URL
$app->post(
    '/urls',
    function ($request, $response) use ($database, $router) {
        // Extract url from the form
        $url = $request->getParsedBodyParam('url');
        $name = $url['name'];

        // Validation
        $v = new Valitron\Validator($url);
        $v->rule('required', 'name') // Field is not empty
        ->rule('url', 'name') // Correct url address
        ->rule('lengthMax', 'name', 255); // Length not more than 255 characters
        // If there are errors, we set the response code to 422 and render the form with errors.
        if (!$v->validate()) {
            $errors = $v->errors();
            $params = [
                'errors' => $errors
            ];
            return $this->get('renderer')->render($response->withStatus(422), 'index.phtml', $params);
        }

        // Add "Страница уже существует" ??????

        // Add url in database table 'urls'
        $timestamp = Carbon::now();
        $queryInsert = $database->prepare('INSERT INTO urls (name, created_at) VALUES (:name, :timestamp)');
        $queryInsert->execute(array(':name' => $name, ':timestamp' => $timestamp));
        // If the data is correct, save, add a flush and redirect
        $this->get('flash')->addMessage('success', 'Страница успешно добавлена');

        return $this->get('renderer')->render($response, 'show.phtml');
        return $response->withRedirect($router->urlFor('url'), 302);
    }
)->setName('postNewUrl');

// ALL URLS
$app->get(
    '/urls',
    function ($request, $response) {
        $itemMenu = 'urls';
        $params = [
            'itemMenu' => $itemMenu
        ];
        return $this->get('renderer')->render($response, 'urls.phtml', $params);
    }
)->setName('urls');

// SHOW INFORM ABOUT ONE URL
$app->get(
    '/urls/{id}',
    function ($request, $response, $args) use ($database) {
        $id = $args['id'];
        $messages = $this->get('flash')->getMessages();

        $querySelect = $database->prepare('SELECT * FROM urls WHERE id=:id')->execute(array(':id' => $id));
        $aboutUrl = $querySelect->fetch();

        $params = [
            'url' => $aboutUrl,
            'flash' => $messages
        ];
        return $this->get('renderer')->render($response, 'show.phtml', $params);
    }
)->setName('url');

$app->run();
