<?php
declare(strict_types=1);

namespace App;

use App\Controllers\TasksController;
use App\Repositories\MySqlTaskRepository;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
session_start();

class Routing
{
    public static function dispatch(): void
    {
        $dbService = new DatabaseService();
        $taskRepository = new MySqlTaskRepository($dbService);

        $twig = $dbService->getTwig();
        $tasksView = new TasksView($twig);

        $controller = new TasksController($dbService, $tasksView, $taskRepository);



        $dispatcher = simpleDispatcher(function (RouteCollector $r) use ($controller) {
            $r->addRoute('GET', '/', [$controller, 'index']);
            $r->addRoute('POST', '/insert', [$controller, 'insert']);
            $r->addRoute('POST', '/task/{id:\d+}', [$controller, 'delete']);
        });

        $httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = isset($_SERVER['REQUEST_URI']) ? rawurldecode($_SERVER['REQUEST_URI']) : '/';


        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                echo '404 Not Found';
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                echo '405 Method Not Allowed';
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $controllerClass = $handler[0];
                $action = $handler[1];

                $controller = new $controllerClass($dbService, $tasksView, $taskRepository);

                $response = $controller->$action($vars);

                echo $response;

                break;
        }
    }
}

